document.addEventListener("DOMContentLoaded", function () {
  /**
   * Form submit event of the landing page
   */
  const form = document.querySelector("#lp_form");
  form?.addEventListener("submit", async function (e) {
    e.preventDefault();
    onError(""); //Clear error field
    const sp = new URLSearchParams(window.location.search);
    const formData = new FormData(this);
    const entries = Object.fromEntries(formData.entries());

    if (!sp.has("id")) {
      //Should never happen
      onError("No id found in URL");
      return;
    }
    setLoader(true);
    const userApi = new UserApi();
    const validUser = await userApi.validateUser({
      uniqId: sp.get("id"),
      userId: entries["userId"],
      userEmail: entries["userEmail"],
    });
    setLoader(false);

    if (!validUser || validUser.error) {
      onError(validUser?.error || "Error submitting form");
      return;
    }

    if (validUser.data) {
      onSuccess(validUser.data);
      return;
    }
  });

  const jsonUploadForm = document.querySelector("#jsonUploadForm");
  const jsonUploadButton = jsonUploadForm?.querySelector(
    "input[type='submit']"
  );
  const sendEmailButton = document.querySelector("#sendEmailBtn");
  const ImportLogArea = document.querySelector("#ImportLogArea");

  function disableForm(status) {
    if (status) {
      sendEmailButton?.setAttribute("disabled", true);
      jsonUploadButton?.setAttribute("disabled", true);
    } else {
      sendEmailButton?.removeAttribute("disabled");
      jsonUploadButton?.removeAttribute("disabled");
    }
  }
  /**
   * Send email button event
   */
  sendEmailButton?.addEventListener("click", async function (e) {
    e.preventDefault();
    console.log(jsonUploadButton);
    const messageArea = document.querySelector("#sendMessageResult");
    const adminApi = new AdminApi();
    //Disable buttons and show loader
    disableForm(true);
    messageArea.innerHTML = "";
    setLoader(true);
    //Send emails
    const { data } = await adminApi.sendEmailsToUsers();
    //Enable buttons and hide loader
    setLoader(false);
    disableForm(false);
    if (data) {
      messageArea.innerHTML = `Total: ${data.totalCount} \nFailed: ${
        data.totalCount - data.successCount
      }`;
    }
  });

  /**
   * Import users data from json file
   */
  jsonUploadForm?.addEventListener("submit", async function (e) {
    e.preventDefault();
    const adminApi = new AdminApi();
    const formData = new FormData(this);
    ImportLogArea.innerHTML = "";
    disableForm(true);
    setLoader(true);
    const result = await adminApi.ImportUsers(formData);
    disableForm(false);
    setLoader(false);
    if (result) {
      ImportLogArea.innerHTML = result;
    }
  });
});

function onError(error) {
  const errorField = document.querySelector("#errorField");
  errorField.textContent = error;
}

function onSuccess(jsonData) {
  if (jsonData.html) {
    const wrapper = document.querySelector("#wrapper");
    wrapper.innerHTML = jsonData.html;
  }
}

function setLoader(isLoading) {
  const loaderArea = document.querySelector("#loaderArea");
  //toggle loader
  const spinner = loaderArea?.querySelector(".spinner");
  if (isLoading) {
    loaderArea.innerHTML = '<div class="spinner"></div>';
  } else if (spinner) {
    spinner.remove();
  }
}
