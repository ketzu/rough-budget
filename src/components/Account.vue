<template>
  <v-card>
    <v-card-title class="pb-0">
      <h3>Online Sync</h3>
    </v-card-title>

    <v-layout row align-center wrap>
      <v-flex xs10 offset-xs1>
        <v-text-field
            :disabled="loggedin"
            v-model="name"
            :error-messages="nameerrors"
            label="Name"
            single-line
        ></v-text-field>
      </v-flex>
      <v-flex xs10 offset-xs1>
        <v-text-field
            :disabled="loggedin"
            v-model="password"
            :append-icon="showpw && !loggedin ? 'far fa-eye-slash' : 'far fa-eye'"
            :type="showpw && !loggedin ? 'text' : 'password'"
            label="Password"
            @click:append="showpw = !showpw"
        ></v-text-field>
      </v-flex>
      <v-flex xs10 offset-xs1>
        <v-btn @click="store()" color="blue darken-2" :disabled="!loggedin" block>
          Store
        </v-btn>
        <v-btn @click="load()" color="orange darken-2" :disabled="!loggedin" block>
          Load
        </v-btn>
      </v-flex>
    </v-layout>

    <v-card-actions>
      <v-dialog v-model="dialog" persistent max-width="600px" v-if="loggedin">
        <template v-slot:activator="{ on }">
          <v-btn v-on="on">
            Delete
          </v-btn>
        </template>
        <v-card>
          <v-card-title>
            <span class="headline">{{translate("Really delete your account?")}}</span>
          </v-card-title>
          <v-card-text>
            <v-container grid-list-md>
              <v-layout wrap>
                <v-flex xs12>
                  {{translate('This will delete your account. This can not be undone.')}}
                  <v-text-field
                      :label="translate('Please insert anything to proceed.')"
                      v-model="confirmation"
                      required
                  ></v-text-field>
                </v-flex>
              </v-layout>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-btn @click="deleteAccount()" color="red darken-2" :disabled="confirmation===''">Delete Account</v-btn>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" @click="dialog = false; confirmation=''">Keep</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-btn @click="registerAccount()" v-if="!loggedin">
        Register
      </v-btn>
      <v-spacer></v-spacer>
      <v-btn @click="logout()" v-if="loggedin">
        Logout
      </v-btn>
      <v-btn @click="login()" v-if="!loggedin" color="blue darken-2">
        Login
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
  import Settings from './settingsmixin'

  const b64tou8a = base64_string => Uint8Array.from(atob(base64_string), c => c.charCodeAt(0));
  const u8atob64 = uint8array => btoa(String.fromCharCode(...uint8array));
  const abtob64 = ab => u8atob64(new Uint8Array(ab));

  export default {
    name: "account",
    data() {
      return {
        authkey: undefined,
        enckey: undefined,
        confirmation: "",
        showpw: false,
        name: "",
        nameerrors: "",
        password: "",
        dialog: false,
        translation: {
          "de": {
            "Really delete your account?": "Account wirklich Löschen?",
            "Please insert anything to proceed.": "Bitte tragen Sie etwas ein um fortzufahren.",
            "This will delete your account. This can not be undone.": "Dies wird ihren Account unwiederruflich löschen.",
            "Name already exists.": "Accountname existiert bereits.",
            "Name or password wrong.": "Name oder Passwort falsch."
          }
        }
      }
    },
    computed: {
      loggedin: {
        get() {
          return this.$store.getters.loggedin;
        }
      }
    },
    methods: {
      deleteAccount() {
        if (this.confirmation !== "") {
          this.logout();
          this.dialog = false;
          this.confirmation = "";
          this.formLike().then(formdata => {
            fetch("https://rough-budget.com/api/delete.php", {
              method: 'POST',
              body: formdata
            }).then(res => res.json())
              .then(({success}) => {
                console.log("Delete: "+success);
                if(success) {
                  // delete Success
                }else{
                  // delete failed
                }
              })
              .catch(error => console.error('Error:', error));
          });
        }
      },
      loginsuccess() {
        this.$store.dispatch('setcredentials', {username: this.name, password: this.password, loggedin: true});
      },
      registerAccount() {
        let self = this;
        this.nameerrors = "";

        // Dispatch registering call
        this.formLike().then(formdata => {
          fetch("https://rough-budget.com/api/register.php", {
            method: 'POST',
            body: formdata
          }).then(res => res.json())
            .then(({success}) => {
              console.log("Register: "+success);
              if(success) {
                self.loginsuccess();
              }else{
                self.nameerrors = self.translate("Name already exists.");
              }
            })
            .catch(error => console.error('Error:', error));
        });
      },
      login() {
        let self = this;
        this.nameerrors = "";

        // Dispatch login call
        this.formLike().then(formdata => {
          fetch("https://rough-budget.com/api/login.php", {
            method: 'POST',
            body: formdata
          }).then(res => res.json())
            .then(({success}) => {
              console.log("Login: "+success);
              if(success) {
                self.loginsuccess();
              }else{
                self.logout();
                self.nameerrors = self.translate("Name or password wrong.");
              }
            });
        });
      },
      logout() {
        this.authkey = undefined;
        this.enckey = undefined;
        this.$store.dispatch('setcredentials', {username: "", password: "", loggedin: false});
      },
      store() {
        let self = this;
        // dispatch store operation
        this.formLike(true).then(formdata => {
          fetch("https://rough-budget.com/api/store.php", {
            method: 'POST',
            body: formdata
          }).then(res => res.json())
            .then(({success}) => {
              console.log("Store: "+success);
              if(success) {
                // Store Success
                // flash check on button
              }else{
                // Store failed
              }
            })
            .catch(error => console.error('Fetch Error:', error));
        }).catch(error => console.error('FormLike Error:', error));
      },
      load() {
        // dispatch load operation
        let self = this;
        this.formLike().then(formdata => {
          fetch("https://rough-budget.com/api/load.php", {
            method: 'POST', // or 'PUT'
            body: formdata
          }).then(res => res.json())
            .then(({success, data}) => {
              console.log("Load: "+success);
              if(success) {
                // Load Success
                let ivAndData = data.split(";");
                let iv = b64tou8a(ivAndData[0]);
                let encdata = b64tou8a(ivAndData[1]);
                window.crypto.subtle.decrypt({name: "AES-GCM",iv: iv,tagLength: 128},self.enckey, encdata)
                  .then(decrypted => {
                    let state = JSON.parse((new TextDecoder()).decode(decrypted));
                    this.$store.dispatch('loadstore', state);
                  })
                  .catch(err =>{
                    console.error("Decrypt error:", err);
                  });
              }else{
                // Load failed
              }
            }).catch(error => console.error('Fetch error:', error));
        }).catch(error => console.error('Formlike error:', error));
      },
      formLike(includeContent = false) {
        let self = this;
        return new Promise((resolve, reject) => {
          // if keys are missing, create them
          self.keys().then(() => {
            // create a FormData version of name+key
            let fd = new FormData();

            fd.append("name", self.name);
            fd.append("pass", self.authkey);

            // Conent needs to be encrypted if used
            if(includeContent) {
              let iv = window.crypto.getRandomValues(new Uint8Array(12));
              window.crypto.subtle.encrypt({name: "AES-GCM",iv: iv},self.enckey,(new TextEncoder()).encode(self.$store.getters.json))
                .then(encrypted => {
                  //returns an ArrayBuffer containing the encrypted data
                  fd.append("data", u8atob64(iv)+";"+abtob64(encrypted));
                  resolve(fd);
                })
                .catch(err => {
                  console.error('Encrypt error:', err);
                  reject("Encryption failed.");
                });
            }else{
              resolve(fd);
            }
          });
        });
      },
      keys() {
        let self = this;
        return new Promise(resolve => {
          if(self.enckey !== undefined && self.authkey !== undefined)
          {
            console.log("Keys do exist.");
            resolve([self.authkey, self.enckey]);
          }
          window.crypto.subtle.importKey("raw", (new TextEncoder()).encode("my password"), {name: "PBKDF2",}, false, ["deriveKey"])
            .then((key) => {
              // Dervie Enc/Dec Key
              let enc = new Promise(resolve => {window.crypto.subtle.deriveKey({name: "PBKDF2",salt: (new TextEncoder()).encode(self.password),iterations: 2500,hash: {name: "SHA-256"},}, key, {name: "AES-GCM", length: 256,}, false, ["encrypt", "decrypt"])
                .then(key => {
                  self.enckey = key;
                  resolve(key);
              });});
              // Derive Auth Key
              let auth = new Promise(resolve => {window.crypto.subtle.deriveKey({name: "PBKDF2",salt: (new TextEncoder()).encode(self.password),iterations: 5000,hash: {name: "SHA-256"},}, key, {name: "AES-GCM", length: 256,}, true, ["encrypt", "decrypt"])
                .then(key => {
                  window.crypto.subtle.exportKey("jwk", key)
                    .then(keydata => {
                      self.authkey = keydata.k;
                      resolve(keydata.k);
                    });
                });});
              Promise.all([auth, enc]).then(() => resolve());
            });
        });
      }
    },
    mounted() {
      this.name = this.$store.getters.username;
      this.password = this.$store.getters.password;
      if(this.loggedin) {
        this.login();
      }
    },
    mixins: [Settings]
  }
</script>

<style scoped>
</style>