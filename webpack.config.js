const path = require("path");

module.exports = {
  mode: "development",
  entry: "./resources/assets/js/app.js",
  output: {
    path: path.resolve("./public/js"),
    filename: "app.min.js",
  },
  module: {
    rules: [
      {
        test: /\.m?js$/,
        exclude: /(node_modules|bower_components|.yarn)/,
        use: {
          loader: "babel-loader",
          options: {
            presets: ["@babel/preset-env", "@babel/preset-react"],
          },
        },
      },
    ],
  },
};
