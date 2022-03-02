
class MicxStreetMapSuggest extends HTMLElement {
  constructor() {
    super();
    this.endpointUrl = '%%ENDPOINT_URL%%';
    this.subscriptionId = '%%SUBSCRIPTION_ID%%';
    this.formEl = null;
  }



  async connectedCallback() {
    await MicxToolsV1.domReady();
    this.formEl = MicxToolsV1.querySelector("#" + this.getAttribute("for"), )

    this.formEl.addEventListener("keydown", async (evt) => {
      await MicxToolsV1.debounce(500, 3000);
      console.log("debounce" + this.formEl.value);
    })

    console.log("dom ready");
  }



}
customElements.define("micx-streetmap-suggest", MicxStreetMapSuggest);
