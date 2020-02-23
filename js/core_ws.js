(function() {
  var socket;
  var init = function() {
    if (typeof core_debug_ws == "undefined") {
      alert(
        "not set ws var. Write\n\r c\\mvc::addJsVar('core_debug_ws', 'ws://localhost:8889');\n\r for example"
      );
      return false;
    }
    socket = new WebSocket(core_debug_ws);

    socket.onopen = function() {
      console.log("connected");
    };
    socket.onmessage = function(e) {
      console.log("Core WS debug: " + e.data);
      setTimeout(
        function() {
          switch (e.data.split(".").pop()) {
            case "sql":
              return;
            case "css":
              [].forEach.call(
                document.querySelectorAll('link[href^="' + e.data + '"]'),
                element =>
                  element.setAttribute("href", e.data + "?t=" + Date.now())
              );
              return;
            case "js":
              let exist = document.querySelector(
                'script[src^="' + e.data + '"]'
              );
              if (exist) {
                exist.parentNode.removeChild(exist);
                var d = window.document,
                  el = d.createElement("script");
                el.async = true;
                el.src = e.data + "?t=" + Date.now();
                d.body.appendChild(el);
              }
              return;
          }
          if (e.data.substr(e.data.length - 9) == "/ajax.php") return true;
          //location.href=location.href; // default
          xhr = new XMLHttpRequest();
          xhr.open("GET", location.href);
          xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
          xhr.onload = function() {
            if (xhr.status === 200) {
              let data = xhr.responseText;
              if (data.indexOf("Core MVC debug finished") == -1) {
                document.write(data);
              } else {
                location.reload(true);
              }
            }
          };
          xhr.send();
        },
        typeof core_debug_ws_latency == "undefined" ? 0 : core_debug_ws_latency
      );
    };
    socket.onerror = function(error) {
      console.log(this);
    };
    socket.onclose = function() {
      socket.close();
      console.log("closed");
      setTimeout(init, 5000);
    };
  };

  return {
    load: function() {
      window.addEventListener(
        "load",
        function() {
          init();
        },
        false
      );
    }
  };
})().load();
