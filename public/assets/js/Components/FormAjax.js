export default class {
  constructor(elem) {
    this.elem = elem;
  }

  start() {
    this.elem.addEventListener("submit", async (event) => {
      const url = this.elem.getAttribute("action");
      event.preventDefault();
      const body = new FormData(this.elem);
      const headers = new Headers({
        Accept: "application/json",
        "X-CSRF-TOKEN": document.querySelector("meta[name='X-CSRF-TOKEN']")
          .getAttribute("content")
      });

      const req = await fetch(url, {headers, body, method: "POST"});
      const resp = await req.json();

      if (req.ok) {
        document.location.reload();
        return;
      } else if (req.status === 422) {
        const errors = resp.errors;

        for (const input of this.elem.elements) {
          const inputName = input.getAttribute("name");
          const invalidFeedback = this.elem.querySelector(`[name="${inputName}"] ~ .invalid-feedback`);
          input.classList.remove("is-invalid");

          if (errors[inputName] && invalidFeedback) {
            input.classList.add("is-invalid");
            invalidFeedback.textContent = errors[inputName][0];
          }
        }
        return;

      }

      alert("Ocorreu um erro inesperado.");
    });
  }
}

