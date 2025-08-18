const usernameInput = document.querySelector("#username-input");
const passwordInput = document.querySelector("#password-input");
const loginBtn = document.querySelector("#loginBtn");
const loginFailed = document.querySelector("#login-failed");

const regBtn = document.querySelector("#regBtn");
const regFailed = document.querySelector("#register-failed");

const showPassword = document.querySelector("#togglePassword");

let users = JSON.parse(localStorage.getItem("userAccounts")) || [];

const accounts = {
  addAccount(username, password) {
    let newUser = {
      username,
      password,
    };

    users.push(newUser);
    localStorage.setItem("userAccounts", JSON.stringify(users));
    console.log("Signup Successfuly!");
  },
  viewAccount() {
    return JSON.parse(localStorage.getItem("userAccounts")) || [];
  },

  clear() {
    localStorage.clear();
  },
};

if (loginBtn) {
  loginBtn.addEventListener("click", (e) => {
    e.preventDefault();

    const userName = usernameInput.value;
    const passWord = passwordInput.value;

    let targetUser = users.find((user) => user.username === userName);

    if (!targetUser) {
      loginFailed.textContent =
        "Wrong Password and Username. Please try Again!";
      console.log("Wrong Username and Password. Please Try Again");
    } else if (passWord === targetUser.password) {
      loginFailed.textContent = "";
      console.log("Login");
      window.location.href = "menu.html";
    } else {
      loginFailed.textContent = "Wrong Password. Try Again!";
      console.log("Wrong Password");
    }
  });
}

if (regBtn) {
  regBtn.addEventListener("click", (e) => {
    e.preventDefault();

    const reg_username = usernameInput.value;
    const reg_password = passwordInput.value;

    let targetUser = users.find((user) => user.username === reg_username);

    if (targetUser) {
      regFailed.textContent = "Username is in use. Please try again!";
    } else {
      accounts.addAccount(reg_username, reg_password);
      window.location.href = "index.html";
    }
  });
}

function showpw() {
  if (passwordInput.type === "password") {
    passwordInput.type = "text";
  } else {
    passwordInput.type = "password";
  }
}

showPassword.addEventListener("click", showpw);
