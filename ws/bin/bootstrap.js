const fs = require("fs");

module.exports = () => {
  return new Promise((resolve, reject) => {
    let modules = [];
    fs.readdir("./ws/packets/", (err, files) => {
      files.forEach((file) => {
        var tmp = require("../packets/" + file);
        console.log("Registered packet " + file);
        modules.push(tmp);
      });
    });
    resolve(modules);
  });
};
