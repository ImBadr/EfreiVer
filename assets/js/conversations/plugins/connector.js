import Vue from "vue";
import VueNativeSock from "vue-native-websocket";

export default (token) => {
  Vue.use(VueNativeSock, "ws://localhost:9845/?token=" + token, {
    format: "json",
    reconnection: true,
  });
};
