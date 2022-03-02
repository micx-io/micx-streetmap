

class MicxToolsV1 {}

/**
 * Wait for DomContentLoaded or resolve immediate
 *
 * @return {Promise<unknown>}
 */
MicxToolsV1.domReady = async ()=> {
  return new Promise((resolve) => {
    if (document.readyState === "complete" || document.readyState === "loaded")
      return resolve("loaded");
    window.addEventListener("DOMContentLoaded", ()=>resolve('DOMContentLoaded'));
  });
}

/**
 * Query a Element or trigger an Exception
 *
 * @param query
 * @param parent
 * @param exception
 * @return {HTMLElement}
 */
MicxToolsV1.querySelector = (query, parent, exception) => {
  if (typeof exception === "undefined")
    exception = `querySelector '${query}' not found`
  if (typeof parent === "undefined" || parent === null)
    parent = document;
  let e = parent.querySelectorAll(query);
  if (e.length === 0) {
    console.warn(exception, "on parent: ", parent);
    throw exception;
  }
  return e[0];
}

MicxToolsV1._debounceInterval = {i: null, time: null};
MicxToolsV1.debounce = async (min, max) => {
  let dbi = MicxToolsV1._debounceInterval;
  return new Promise((resolve) => {
    if (dbi.time < (+new Date()) - max && dbi.i !== null) {
      return resolve();
    }
    if (dbi.i !== null) {
      return;
    }
    dbi.time = (+new Date());
    dbi.i = window.setTimeout(() => {
      dbi.i = null;
      return resolve('done');

    }, min);
  });

}
