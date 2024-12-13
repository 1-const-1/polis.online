import React from "react";
import ReactDOM from "react-dom/client";
import App from "./App";

let elm = document.getElementById("root");

if (!elm) {
  elm = document.createElement("div");
  elm.id = "root";
  document.body.replaceChildren(elm);
}

const root = ReactDOM.createRoot(elm);
root.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
); 
