window.Wicket = function(doc, tag, id, script) {
  var w = window.Wicket || {};
  if (doc.getElementById(id)) return w;
  var ref = doc.getElementsByTagName(tag)[0];
  var js = doc.createElement(tag);
  js.id = id;
  js.src = script;
  ref.parentNode.insertBefore(js, ref);
  w._q = [];
  w.ready = function(f) {
    w._q.push(f)
  };
  return w
}(document, "script", "wicket-widgets", drupalSettings.wicket_contact_information.wicket_admin_react_url);
