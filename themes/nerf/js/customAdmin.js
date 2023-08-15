const links = document.querySelectorAll('[href*="action=edit"]');
links.forEach((link) => {
  newLink = link
    .getAttribute("href")
    .replace("action=edit", "action=elementor");
  link.setAttribute("href", newLink);
});
