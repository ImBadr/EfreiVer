import Vue from "vue";
import "babel-polyfill";
import "./conversations/plugins/bus";
import connector from "./conversations/plugins/connector";
import "./conversations/plugins/bus";
import "./conversations/plugins/modal";
import "./conversations/plugins/notif";
import "material-icons/iconfont/material-icons.css";

try {
  if (USER_JWT_TOKEN) {
    console.log("connector");
    connector(USER_JWT_TOKEN);
  }
} catch (ignored) {}

new Vue({
  el: "#app",
  delimiters: ["${", "}"],
  components: components,
  data: {},
  mounted() {},
});
