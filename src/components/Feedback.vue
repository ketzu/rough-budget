<template>
  <div class="row bg-primary text-light settings">
    <div class="col-md-12">
      <form>
        <div class="form-group">
          <label for="feedbackText">{{translate("Do you have problems or a feature request?")}}</label>
          <textarea v-model="feedback" class="form-control" id="feedbackText" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-outline-info float-right" @click.prevent="send" style="margin-bottom: 10px;">{{translate("Submit")}}</button>
      </form>
    </div>
  </div>
</template>

<script>
  import Settings from './settingsmixin'

  export default {
    name: "feedback",
    mixins: [Settings],
    data() {
      return {
        feedback: '',
        status: '',
        request: false,
        translation: {
          "de": {
            "Do you have problems or a feature request?": "Haben Sie eine Frage oder wünschen Sie eine bestimmte Funktionalität?",
            "Submit": "Absenden"
          }
        }
      }
    },
    methods: {
      send() {
        if (this.feedback === '') {
          return;
        }
        let http = new XMLHttpRequest();
        let url = "https://rough-budget.com/feedback.php";
        let params = "feedback="+this.feedback;
        http.open("POST", url, true);

        // Send the proper header information along with the request
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        this.$emit("feedback-submit");

        http.onreadystatechange = function() {
          if (http.readyState >= 2 && http.status === 200) {
            this.feedback = '';
            this.request = false;
          }
        };
        http.send(params);
      }
    }
  }
</script>

<style scoped>

</style>