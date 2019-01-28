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
        <v-btn slot="activator">
          Delete
        </v-btn>
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

  export default {
    name: "account",
    data() {
      return {
        password: "",
        name: "",
        confirmation: "",
        showpw: false,
        loggedin: false,
        dialog: false,
        translation: {
          "de": {
            "Really delete your account?": "Account wirklich Löschen?",
            "Please insert anything to proceed.": "Bitte tragen Sie etwas ein um fortzufahren.",
            "This will delete your account. This can not be undone.": "Dies wird ihren Account unwiederruflich löschen."
          }
        }
      }
    },
    methods: {
      deleteAccount() {
        if(this.confirmation !== "") {
          alert("DELETED!");
          this.dialog = false;
          this.loggedin = false;
          this.confirmation = "";
        }
      },
      registerAccount() {
        this.loggedin = true;
      },
      login() {
        this.loggedin = true;
      },
      logout() {
        this.loggedin = false;
      },
      store() {

      },
      load() {

      }
    },
    mixins: [Settings]
  }
</script>

<style scoped>
</style>