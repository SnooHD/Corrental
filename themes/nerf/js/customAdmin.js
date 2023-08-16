const links = document.querySelectorAll('[href*="action=edit"]');
links.forEach((link) => {
  newLink = link
    .getAttribute("href")
    .replace("action=edit", "action=elementor");
  link.setAttribute("href", newLink);
});

const wpmlSettings = document.querySelectorAll(
  ".wpml-settings-container div, .wpml-settings-container form, .wpml-settings-container ul"
);
wpmlSettings.forEach((setting) => {
  if (setting.classList.has("wpml-section-hide-languages")) return;

  setting.style.display = "none";
});
