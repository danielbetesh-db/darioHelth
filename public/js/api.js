// Base class for api calls
class Api {
  constructor() {
    this.url = window.location.origin + "/dario/public/api/";
  }
  async fetchData(method, data) {
    try {
      const res = await fetch(this.url, {
        body: JSON.stringify(data),
        method: method,
        headers: {
          "Content-Type": "application/json",
        },
        cache: "no-cache",
      });

      if (!res?.ok) {
        return null;
      }

      return await res.json();
    } catch (err) {
      console.log(err);
      return null;
    }
  }

  async fetchFormData(method, formData) {
    try {
      const res = await fetch(this.url, {
        body: formData,
        method: method,
        cache: "no-cache",
      });

      if (!res?.ok) {
        return null;
      }
      return await res.text();
    } catch (err) {
      console.log(err);
      return null;
    }
  }
}
class UserApi extends Api {
  constructor() {
    super();
    this.url = this.url + "user.php";
  }

  //Validate user, and return user data with devices and html template from server
  async validateUser({ uniqId, userEmail, userId }) {
    const jsonResult = await this.fetchData("POST", {
      uniqId,
      userEmail,
      userId,
    });
    if (jsonResult) {
      return jsonResult;
    } else {
      return null;
    }
  }
}

class AdminApi extends Api {
  constructor() {
    super();
    this.url = this.url + "admin.php";
  }
  // Send emails to not approved users
  async sendEmailsToUsers() {
    const jsonResult = await this.fetchData("POST", {
      action: "sendEmailsToUsers",
    });

    if (jsonResult) {
      return jsonResult;
    } else {
      return null;
    }
  }

  async ImportUsers(formData) {
    formData.append("action", "importUsers");
    const jsonResult = await this.fetchFormData("POST", formData);
    if (jsonResult) {
      return jsonResult;
    } else {
      return null;
    }
  }
}
