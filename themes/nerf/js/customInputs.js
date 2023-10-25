/**
 * Connect all input fields
 */
document.addEventListener("DOMContentLoaded", () => {
  const inputFields = document.querySelectorAll(
    '[name*="name"], [name*="email"], [name*="phone"]'
  );

  if (inputFields.length === 0) return;

  const updateValues = (e) => {
    const value = e.currentTarget.value;
    let name = e.currentTarget.name.replace("your-", "");
    if (name.endsWith("2")) name = name.slice(0, -1);

    const associatedInputs = document.querySelectorAll(`[name*="${name}"]`);
    associatedInputs.forEach((input) => {
      input.value = value;
    });
  };

  inputFields.forEach((input) => {
    input.addEventListener("input", updateValues);
  });

  // Add hidden input to booking
  const langInput = document.querySelector(
    '.wpdev-form-control-wrap [name^="lang"]'
  );

  langInput.setAttribute("hidden", "true");
  const language = document
    .querySelector("html")
    .getAttribute("lang")
    .replace("-", "_");
  langInput.value = language;
});
