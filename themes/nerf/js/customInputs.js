/**
 * Connect all input fields
 */

const inputFields = document.querySelectorAll(
  '[name^="your-name"], [name^="your-email"], [name^="your-phone"]'
);

const updateValues = (e) => {
  const value = e.currentTarget.value;
  let name = e.currentTarget.name;
  if (name.endsWith("2")) name = name.slice(0, -1);

  const associatedInputs = document.querySelectorAll(`[name^="${name}"]`);
  associatedInputs.forEach((input) => {
    input.value = value;
  });
};

inputFields.forEach((input) => {
  input.addEventListener("input", updateValues);
});
