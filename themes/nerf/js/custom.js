document.addEventListener("DOMContentLoaded", () => {
  document
    .querySelector('#booking_form_div2 input[type="button"]')
    .addEventListener("click", () => {
      document
        .querySelector("#booking_form_div2 .loader")
        .classList.add("active");
    });
});
