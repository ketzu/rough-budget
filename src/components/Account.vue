<template>
  <v-form>
    <v-text-field
        :disabled="loggedin"
        v-model="name"
        :error-messages="nameerrors"
        label="Name"
        single-line
    ></v-text-field>
    <v-text-field
        :disabled="loggedin"
        v-model="password"
        :append-icon="showpw && !loggedin ? 'far fa-eye-slash' : 'far fa-eye'"
        :type="showpw && !loggedin ? 'text' : 'password'"
        label="Password"
        @click:append="showpw = !showpw"
    ></v-text-field>

    <v-btn @click="store()" color="blue-darken-2" :disabled="!loggedin" block class="text-white">
      Store
    </v-btn>
    <v-btn @click="load()" color="orange-darken-4" :disabled="!loggedin" block class="mt-2 text-white">
      Load
    </v-btn>

    <v-dialog v-model="dialog" persistent max-width="600px" v-if="loggedin">
      <template #activator="{ props }">
        <v-btn v-bind="props" class="mt-2" block>
          Delete
        </v-btn>
      </template>
      <v-card>
        <v-card-title>
          <span class="headline">Do you really want to delete your account?</span>
        </v-card-title>
        <v-card-text>
          <v-container>
              <v-row>
                <h2>
                  This will delete your account and remove all data from our database. This can not be undone.
                </h2>
              </v-row>
              <v-row>
                <v-text-field
                    label="Please insert anything to proceed."
                    v-model="confirmation"
                    required
                ></v-text-field>
            </v-row>
            <v-row>
              <v-btn @click="deleteAccount()" color="red-darken-2" :disabled="confirmation===''" block>Delete Account</v-btn>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-btn color="blue-darken-1" @click="dialog = false; confirmation=''" block>Keep</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-btn @click="registerAccount()" v-if="!loggedin" block class="mt-2">
      Register
    </v-btn>

    <v-btn @click="logout()" v-if="loggedin" block class="mt-2">
      Logout
    </v-btn>
    <v-btn @click="login()" v-if="!loggedin" block class="mt-2">
      Login
    </v-btn>

  </v-form>
</template>

<script>
  import Settings from './settingsmixin'
  import { apiRequest } from '../api'
  import {
    deriveKeys,
    deriveLegacyAuthenticationKey,
    deriveLegacyEncryptionKey,
    fromBase64,
    PROTOCOL_VERSION
  } from '../security/keys'

  const b64tou8a = base64_string => Uint8Array.from(atob(base64_string), c => c.charCodeAt(0));
  const u8atob64 = uint8array => btoa(String.fromCharCode(...uint8array));
  const abtob64 = ab => u8atob64(new Uint8Array(ab));

  export default {
    name: "account",
    data() {
      return {
         authkey: undefined,
         enckey: undefined,
         protocol: undefined,
         legacyAuthkey: undefined,
         legacyEnckey: undefined,
        confirmation: "",
        showpw: false,
        name: "",
        nameerrors: "",
        password: "",
        dialog: false
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
      async deleteAccount() {
        if (this.confirmation !== "") {
          this.dialog = false;
          this.confirmation = "";
          try {
            const formdata = await this.formLike();
            const {success} = await apiRequest("/api/delete.php", {
              method: 'POST',
              body: formdata
            });
            console.log("Delete: "+success);
            if (success) this.logout();
          } catch (error) {
            console.error('Delete error:', error);
          }
        }
      },
      loginsuccess() {
        this.$store.dispatch('setcredentials', {username: this.name, loggedin: true, password: this.password});
      },
      async registerAccount() {
        this.nameerrors = "";

        // Dispatch registering call
        try {
          const parameters = await apiRequest("/api/salt.php", {
            method: "POST",
            body: JSON.stringify({name: this.name, registration: true}),
            headers: {"Content-Type": "application/json"}
          });
          this.protocol = {version: PROTOCOL_VERSION, salt: parameters.salt};
          const formdata = await this.formLike();
          formdata.append("kdf_salt", this.protocol.salt);
          const {success} = await apiRequest("/api/register.php", {
            method: 'POST',
            body: formdata
          });
          console.log("Register: "+success);
          if (success) {
            this.loginsuccess();
          } else {
            this.nameerrors = "Registration could not be completed, maybe the account already exists or you left some fields empty.";
          }
        } catch (error) {
          console.error('Register preparation error:', error);
          this.nameerrors = error.message || error;
        }
      },
      async login() {
        this.nameerrors = "";

        // Dispatch login call
        try {
          const formdata = await this.formLike(false, true);
          formdata.append("kdf_salt", this.protocol.salt);
          const {success} = await apiRequest("/api/login.php", {
            method: 'POST',
            body: formdata
          });
          if (success) {
            this.loginsuccess();
          } else {
            this.logout();
            this.nameerrors = "Name or password wrong.";
          }
        } catch (error) {
          console.error('Login preparation error:', error);
          this.logout();
          this.nameerrors = error.message || error;
        }
      },
      logout() {
        this.authkey = undefined;
        this.enckey = undefined;
        this.protocol = undefined;
        this.legacyAuthkey = undefined;
        this.legacyEnckey = undefined;
        this.$store.dispatch('setcredentials', {username: "", password: "", loggedin: false});
      },
      async store() {
        // dispatch store operation
        try {
          const formdata = await this.formLike(true);
          const {success} = await apiRequest("/api/store.php", {
            method: 'POST',
            body: formdata
          });
          console.log("Store: "+success);
          if (!success) this.nameerrors = "Store failed.";
        } catch (error) {
          console.error('Store error:', error);
          this.nameerrors = "Store failed.";
        }
      },
      async load() {
        // dispatch load operation
        try {
          const formdata = await this.formLike();
          const {success, data} = await apiRequest("/api/load.php", {
            method: 'POST', // or 'PUT'
            body: formdata
          });
          console.log("Load: "+success);
          if (!success) {
            this.nameerrors = "Load failed.";
            return;
          }

          const ivAndData = data.split(";");
          const iv = b64tou8a(ivAndData[0]);
          const encdata = b64tou8a(ivAndData[1]);
          const decrypt = key => window.crypto.subtle.decrypt(
            {name: "AES-GCM",iv: iv,tagLength: 128}, key, encdata
          );
          let decrypted;
          let migrated = false;
          try {
            decrypted = await decrypt(this.enckey);
          } catch {
            if (!this.legacyEnckey) throw new Error("Decrypt failed");
            decrypted = await decrypt(this.legacyEnckey);
            migrated = true;
          }
          const state = JSON.parse((new TextDecoder()).decode(decrypted));
          this.$store.dispatch('loadstore', state);
          if (migrated) await this.store();
        } catch (error) {
          console.error('Load error:', error);
          this.nameerrors = "Load failed.";
        }
      },
      async formLike(includeContent = false, includeLegacyCredentials = false) {
        await this.keys();
        const fd = new FormData();

        fd.append("name", this.name);
        fd.append("pass", this.authkey);
        if (includeLegacyCredentials) {
          fd.append("legacy_pass", this.legacyAuthkey);
        }

        if (includeContent) {
          const iv = window.crypto.getRandomValues(new Uint8Array(12));
          const encrypted = await window.crypto.subtle.encrypt(
            {name: "AES-GCM",iv: iv},
            this.enckey,
            (new TextEncoder()).encode(this.$store.getters.json)
          );
          fd.append("data", u8atob64(iv)+";"+abtob64(encrypted));
        }
        return fd;
      },
      async keys() {
        if (this.enckey !== undefined && this.authkey !== undefined) {
          return [this.authkey, this.enckey];
        }
        if (!this.protocol) {
          const parameters = await apiRequest("/api/salt.php", {
            method: "POST",
            body: JSON.stringify({name: this.name}),
            headers: {"Content-Type": "application/json"}
          });
          this.protocol = {
            version: PROTOCOL_VERSION,
            salt: parameters.salt
          };
          if (!this.protocol.salt) throw new Error("Server returned no salt");
          return this.keys();
        }
        const salt = fromBase64(this.protocol.salt);
        const deriveNewKeys = deriveKeys(this.password, salt, this.protocol.version);
        const deriveLegacyAuth = deriveLegacyAuthenticationKey(this.password);
        const deriveLegacyEncryption = deriveLegacyEncryptionKey(this.password);
        const [keys, legacyAuthkey, legacyEnckey] = await Promise.all([
          deriveNewKeys,
          deriveLegacyAuth,
          deriveLegacyEncryption
        ]);
        this.authkey = keys.authenticationKey;
        this.enckey = keys.encryptionKey;
        this.legacyAuthkey = legacyAuthkey;
        this.legacyEnckey = legacyEnckey;
        return [this.authkey, this.enckey];
      },
    },
    mounted() {
      this.name = this.$store.getters.username;
      this.password = this.$store.getters.password;
    },
    mixins: [Settings]
  }
</script>

<style scoped>
</style>
