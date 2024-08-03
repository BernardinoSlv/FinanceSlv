import FormAjax from "./Components/FormAjax.js";

window.addEventListener("load", () => {
  document.querySelectorAll('[data-js-component=form-ajax]').forEach(elem => {
    new FormAjax(elem).start();
  })
})
