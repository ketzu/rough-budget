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

  const b64tou8a = base64_string => Uint8Array.from(atob(base64_string), c => c.charCodeAt(0));
  const u8atob64 = uint8array => btoa(String.fromCharCode(...uint8array));
  const abtob64 = ab => u8atob64(new Uint8Array(ab));

  export default {
    name: "account",
    data() {
      return {
        authkey: "",
        enckey: "",
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
          this.dialog = false;
          this.loggedin = false;
          this.confirmation = "";
          this.formLike().then(formdata => {
            fetch("./api/delete.php", {
              method: 'POST',
              body: formdata
            }).then(res => res.json())
              .then(({success}) => {
                console.log("Load operation: "+success);
                if(success) {
                  // Load Success
                }else{
                  // Load failed
                }
              })
              .catch(error => console.error('Error:', error));
          });
        }
      },
      loginsuccess() {
        this.loggedin = true;
        this.$store.dispatch('setcredentials', {username: this.name, password: this.password, loggedin: true});
      },
      registerAccount() {
        let self = this;
        this.nameerrors = "";
        this.formLike().then(formdata => {
          fetch("./api/register.php", {
            method: 'POST',
            body: formdata
          }).then(res => res.json())
            .then(({success}) => {
              console.log("Register operation: "+success);
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

        window.crypto.subtle.importKey("raw",(new TextEncoder()).encode("my password"),{name: "PBKDF2",},false,["deriveKey"])
          .then((key) => {
            // Dervie Enc/Dec Key
            window.crypto.subtle.deriveKey({name: "PBKDF2",salt: (new TextEncoder()).encode(self.name),iterations: 2500,hash: {name: "SHA-256"},},key,{name: "AES-GCM",length: 256,},false,["encrypt", "decrypt"])
              .then(key => {
                self.enckey = key;
              });
            // Derive Auth Key
            window.crypto.subtle.deriveKey({name: "PBKDF2",salt: (new TextEncoder()).encode(self.name),iterations: 5000,hash: {name: "SHA-256"},},key,{name: "AES-GCM",length: 256,},true,["encrypt", "decrypt"])
              .then(key => {
                window.crypto.subtle.exportKey("jwk",key)
                  .then(keydata => {
                    self.authkey = keydata.k;

                    // Dispatch login call
                    this.formLike().then(formdata => {
                      fetch("./api/login.php", {
                        method: 'POST',
                        body: formdata
                      }).then(res => res.json())
                        .then(({success}) => {
                          console.log("Login operation: "+success);
                          if(success) {
                            self.loginsuccess();
                          }else{
                            self.loggedin = false;
                            self.nameerrors = self.translate("Name or password wrong.");
                          }
                        })
                        .catch(error => console.error('Error:', error));
                    });
                  });
              });
          });
      },
      logout() {
        this.$store.dispatch('setcredentials', {username: "", password: "", loggedin: false});
      },
      store() {
        let self = this;
        // dispatch store operation
        this.formLike(true).then(formdata => {
          fetch("./api/store.php", {
            method: 'POST',
            body: formdata
          }).then(res => res.json())
            .then(({success}) => {
              console.log("Store operation: "+success);
              if(success) {
                // Store Success
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
          fetch("./api/load.php", {
            method: 'POST', // or 'PUT'
            body: formdata
          }).then(res => res.json())
            .then(({success, data}) => {
              console.log("Load operation: "+success);
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
        return new Promise(function(resolve, reject) {
          // create a FormData version of name+key
          let fd = new FormData();

          fd.append("name", self.name);
          fd.append("pass", self.authkey);
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