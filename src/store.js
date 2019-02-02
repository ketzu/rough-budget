import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

let filter = (obj, fn) => {
  let result = {};
  for(const key in obj) {
    if(obj.hasOwnProperty(key) && fn(obj[key]))
      result[key] = obj[key];
  }
  return result;
};

let typed = (obj, type) => {
  let result = {};
  for(const key in obj) {
    if(obj.hasOwnProperty(key)){
      result[key] = obj[key];
      result[key]["type"] = type;
    }
  }
  return result;
};

let acummulator = (obj) => Object.keys(obj).map(key => obj[key].value/obj[key].steps*(obj[key].spending?-1:1)).reduce((sum, x) => sum+x, 0);
let spendingacummulator = (obj, spending) => Object.keys(obj).map(key => spending===obj[key].spending? obj[key].value/obj[key].steps:0).reduce((sum, x) => sum+x, 0);
let fullacummulator = (obj, spending) => {
  return spendingacummulator(obj.daily, spending)*365+
    spendingacummulator(obj.weekly, spending)*365/7+
    spendingacummulator(obj.monthly, spending)*12+
    spendingacummulator(obj.yearly, spending);
};

let mergedentries = entries => {
  return {...typed(entries.daily, "daily"),
    ...typed(entries.weekly,"weekly"),
    ...typed(entries.monthly, "monthly"),
    ...typed(entries.yearly,"yearly")};
};

export default new Vuex.Store({
  state: {
    settings: { currency: '$', lang: 'en', precision: 0, separator: ',', window: 14, locale: 'en-US', dual: false },
    multiplier: { daily: 365/12, weekly: 365/(7*12), monthly: 1, yearly: 1/12 },
    username: "",
    password: "",
    loggedin: false,
    // what entries should look like: {name: 'Job', spending: false, value: 0, steps: 1}
    entries: {
      daily: {},
      weekly: {},
      monthly: {},
      yearly: {}
    },
    // what trackings should look like: {name: 'Job', type: 'daily', values: []}
    trackings: []
  },
  getters: {
    json(state) {return JSON.stringify(state); },
    currency(state) { return state.settings.currency; },
    dual(state) { return state.settings.dual; },
    username(state) { return state.username; },
    password(state) { return state.password; },
    loggedin(state) { return state.loggedin; },
    locale(state) { return state.settings.locale; },
    localecurrency(state) { return state.settings.localecurrency; },
    precision(state) { return state.settings.precision; },
    separator(state) { return state.settings.separator; },
    lang(state) { return state.settings.lang; },
    window(state) { return state.settings.window; },
    entries(state) { return state.entries; },
    daily(state) {   return state.entries.daily;   },
    weekly(state) {  return state.entries.weekly;  },
    monthly(state) { return state.entries.monthly; },
    income(state) {
      return filter(mergedentries(state.entries), obj => !obj.spending);
    },
    expense(state) {
      return filter(mergedentries(state.entries), obj => obj.spending);
    },
    yearly(state) {  return state.entries.yearly;  },
    trackings(state) {  return state.trackings; },
    multiplier(state) {  return state.multiplier; },
    balance(state) {
      let accumulator = 0;
      for (const [key, value] of Object.entries(state.entries)) {
        accumulator += (acummulator(value)*state.multiplier[key]);
      }
      return accumulator;
    },
    balances(state) {
      return {
        daily: acummulator(state.entries.daily),
        weekly: acummulator(state.entries.weekly),
        monthly: acummulator(state.entries.monthly),
        yearly: acummulator(state.entries.yearly),
        income: fullacummulator(state.entries, false)/12,
        expense: (-1)*fullacummulator(state.entries, true)/12
      }
    },
    incomes(state) {
      return {
        daily: spendingacummulator(state.entries.daily, false),
        weekly: spendingacummulator(state.entries.weekly, false),
        monthly: spendingacummulator(state.entries.monthly, false),
        yearly: spendingacummulator(state.entries.yearly, false)
      }
    },
    expenses(state) {
      return {
        daily: spendingacummulator(state.entries.daily, true),
        weekly: spendingacummulator(state.entries.weekly, true),
        monthly: spendingacummulator(state.entries.monthly, true),
        yearly: spendingacummulator(state.entries.yearly, true)
      }
    },
    anyEntries(state) {
      return Object.keys(state.entries).map(key => state.entries[key]).some(obj => Object.keys(obj).length>0);
    }
  },
  mutations: {
    initstore(state) {
      if (localStorage.getItem('entries')) {
        // act on budget-v1 entries!
        JSON.parse(localStorage.entries).forEach((entries, i) => {
          var category = ['daily', 'weekly', 'monthly', 'yearly'][i - 1];
          entries.forEach(e => {
            state.entries[category][e[0]] = {name:e[0], value: Math.abs(e[1]), spending : e[1]<0, steps: 1};
          });
        });
        // delete budget-v1 style entry
        localStorage.removeItem('entries');
      }
      // budget-v2 part
      if (localStorage.getItem('language')) {
        localStorage.removeItem('language');
      }
      if (localStorage.getItem('currency_symbol')) {
        state.settings.currency = localStorage['currency_symbol'];
        localStorage.removeItem('currency_symbol');
      }
      ['Daily', 'Weekly', 'Monthly', 'Yearly'].forEach(frame => {
        if (localStorage.getItem(frame)) {
          for (let entry of JSON.parse(localStorage[frame])) {
            let obj = JSON.parse(localStorage[entry]);
            state.entries[frame.toLowerCase()][obj.name] = Object.assign({steps: 1}, obj);
            localStorage.removeItem(entry);
          }
          localStorage.removeItem(frame);
        }
      });
      // Defaults section
      // Language
      let lang;
      if (navigator.languages !== undefined) {
        lang = navigator.languages[0];
      } else {
        lang = navigator.language;
      }
      if (["de", "en"].includes(lang.substring(0, 2))) {
        state.settings.lang = lang.substring(0, 2);
      }
      // locale
      state.settings.locale = lang;
      // could use to determine floating point separator: const decimal = (1.5).toLocaleString();
      // Thousands separator
      const thousands = (1234).toLocaleString();
      switch (thousands[1]) {
        case ".": state.settings.separator = "."; break;
        case "-": state.settings.separator = "-"; break;
        default: state.settings.separator = ","; break;
      }
      // Currency
      // Default is USD
      state.settings.currency = "$";
      if (lang.includes("GB")) {
        state.settings.currency = "£";
      }
      if (["de", "it", "fr", "es"].includes(lang.substring(0, 2))) {
        state.settings.currency = "€";
      }

      // Check if the ID exists
      if (localStorage.getItem('budget-v3')) {
        // Replace the state object with the stored item
        this.replaceState(
          Object.assign(state, JSON.parse(localStorage.getItem('budget-v3')))
        );
      }
    },
    loadstore(state, newstate) {
      console.log(state);
      console.log(newstate);
      // Replace the state object with the loaded item
      this.replaceState(Object.assign(state, newstate));
    },
    setcurrency(state, symbol) {
      state.settings.currency = symbol;
    },
    setdual(state, dual) {
      state.settings.dual = dual;
    },
    setcredentials(state, {password, username, loggedin}) {
      state.password = password;
      state.username = username;
      state.loggedin = loggedin;
    },
    setprecision(state, precision) {
      state.settings.precision = precision;
    },
    setlang(state, lang) {
      state.settings.lang = lang;
    },
    setseparator(state, symbol) {
      state.settings.separator = symbol;
    },
    setwindow(state, window) {
      state.settings.window = window;
    },
    updatespending(state, payload) {
      state.entries[payload.type][payload.identity].spending = payload.value;
    },
    updatevalue(state, payload) {
      state.entries[payload.type][payload.identity].value = payload.value;
    },
    updatesteps(state, payload) {
      state.entries[payload.type][payload.identity].steps = payload.value;
    },
    updatename(state, payload) {
      state.entries[payload.type][payload.identity].name = payload.value;
    },
    newentry(state, {type, name, spending}) {
      Vue.set(state.entries[type], name, {name: name, value: 0, steps: 1, spending: spending});
    },
    delentry(state, {type, identity}) {
      Vue.delete(state.entries[type], identity);
      const tracking = state.trackings.map(obj => obj.type+obj.name).findIndex((value) => value===(type+identity));
      if (tracking !== -1) {
        Vue.delete(state.trackings, tracking);
      }
    },
    newtracking(state, {type, identity}) {
      const tracking = state.trackings.map(obj => obj.type+obj.name).findIndex((value) => value===(type+identity));
      if (tracking === -1) {
        state.trackings.push({name: identity, type: type, values: []});
      }
    },
    deltracking(state, index) {
      Vue.delete(state.trackings, index);
    },
    addtrackentry(state, {value, identity, type, date}) {
      const tracking = state.trackings.map(obj => obj.type+obj.name).findIndex((value) => value===(type+identity));
      state.trackings[tracking].values.push({value: value, date: date});
    },
    removetrackentry(state, {value, identity, type, date}) {
      const tracking = state.trackings.map(obj => obj.type+obj.name).findIndex((value) => value===(type+identity));
      const element = state.trackings[tracking].values.findIndex((elem) => elem.value===value && elem.date===date);
      state.trackings[tracking].values.splice(element, 1);
    },
    moveentry(state, {identity, type, to}) {
      let newidentity = identity;
      while(state.entries[to].hasOwnProperty(newidentity)) {
        newidentity = newidentity.concat(Math.floor(" "+Math.random()*1000));
      }
      state.entries[to][newidentity] = state.entries[type][identity];
      Vue.delete(state.entries[type], identity);
    }
  },
  actions: {
    initstore({commit}) {
      commit('initstore');
    },
    loadstore({commit}, payload) {
      commit('loadstore', payload);
    },
    setcurrency({commit}, symbol) {
      commit('setcurrency', symbol);
    },
    setdual({commit}, dual) {
      commit('setdual', dual);
    },
    setcredentials({commit}, payload) {
      commit('setcredentials', payload);
    },
    setprecision({commit}, precision) {
      commit('setprecision', precision);
    },
    setlang({commit}, lang) {
      commit('setlang', lang);
    },
    setseparator({commit}, symbol) {
      commit('setseparator', symbol);
    },
    setwindow({commit}, window) {
      commit('setwindow', window);
    },
    updatespending({commit}, payload) {
      commit('updatespending', payload);
    },
    updatevalue({commit}, payload) {
      commit('updatevalue', payload);
    },
    updatesteps({commit}, payload) {
      commit('updatesteps', payload);
    },
    updatename({commit}, payload) {
      commit('updatename', payload);
    },
    newentry({commit}, payload) {
      commit('newentry', payload);
    },
    delentry({commit}, payload) {
      commit('delentry', payload);
    },
    newtracking({commit}, payload) {
      commit('newtracking', payload);
    },
    deltracking({commit}, index) {
      commit('deltracking', index);
    },
    addtrackentry({commit}, payload) {
      commit('addtrackentry', payload);
    },
    removetrackentry({commit}, payload) {
      commit('removetrackentry', payload);
    },
    moveentry({commit}, payload) {
      commit('moveentry', payload);
    }
  }
})