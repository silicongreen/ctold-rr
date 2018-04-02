self.addEventListener('message', function(e) {
    console.log("askd");
  self.postMessage(e.data);
}, false);