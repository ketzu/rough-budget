<template>
  <v-footer dark app absolute>
    <v-container fluid>
      <v-row justify="center" align="start">
      <v-dialog v-model="dialog" scrollable max-width="600px">
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" text>Privacy Policy</v-btn>
        </template>
        <v-card>
          <v-card-title>Privacy Policy</v-card-title>
          <v-divider></v-divider>
          <v-card-text>
            <v-container>
                <v-row>
                  <p>
                    By using our service, you agree to accept the usage and collection of data in the following ways: Accessing our website does not require registration. By accessing our website our servers log information, such as the name of the requested file and date and time. This information will only be processed for error reports.
                  </p>
                  <p>
                    Identifying data, such as your name, adress or e-mail address is only collected if you register an account on our website. These are provided voluntarily and only if you use this information as your username. No information will be shared or processed by third parties, besides the server hoster Strato.
                  </p>
                  <p>
                    Other identifying data, especially all data used withing the app, is only transmitted to the server if you register an account. If you do so, the data will be encrypted with your password and can not be decrypted by us. Your password is not transmitted in any form that can be read by us.
                  </p>
                </v-row>
            </v-container>

            <v-divider></v-divider>

            <v-container>
                <v-row>
                  <h2>Responsible: David Mödinger</h2>
                </v-row>
              <v-row>
                <v-col cols="2" class="text-right">
                  <v-icon>fas fa-home</v-icon>
                </v-col>
                <v-col>
                  <h4>Römerstr. 118, 89077 Ulm, Germany</h4>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="2" class="text-right">
                  <v-icon>fas fa-envelope</v-icon>
                </v-col>
                <v-col>
                  <h4>contact@rough-budget.com</h4>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="2" class="text-right">
                  <v-icon>fas fa-phone</v-icon>
                </v-col>
                <v-col>
                  <h4>+49 - 177 299 1047</h4>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
          <v-divider></v-divider>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-2" text @click="dialog = false">Close</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <a href="https://paypal.me/roughbudget" target="_blank"><v-btn text>Support us</v-btn></a>
      <a href="mailto:contact@rough-budget.com"> <v-btn text>Contact</v-btn></a>

      </v-row>
    </v-container>
  </v-footer>
</template>

<script>
  export default {
    name: "Footer",
    data() {
      return {
        dialog: false
      }
    }
  }
</script>

<style scoped>

</style>