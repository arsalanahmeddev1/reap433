(function () {
  var myElement = document.getElementById("simple-bar");
  if (!myElement || typeof SimpleBar === "undefined") {
    return;
  }

  new SimpleBar(myElement, { autoHide: false });
})();
