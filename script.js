const showStick = (error) => {
  const stick = document.getElementsByClassName("stick");
  stick[0].style.display = "block";
  stick[0].innerHTML = `<p>${error}</p>`;
  setTimeout(() => {
    stick[0].style.display = "none";
    stick[0].innerHTML = ``;
  }, 5000);
};

console.log();

const loginMe = async (event) => {
  event.preventDefault();
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;
  const my_data = [username, password];
  const formData = new FormData();
  formData.append("my_data", JSON.stringify(my_data));
  const response = await fetch(`server/get-cv.php`, {
    method: "post",
    body: formData,
  });
  const data = await response.json();
  if (data.error.length > 0) {
    showStick(data.error[0]);
  } else {
    sessionStorage.removeItem("user_data");
    sessionStorage.setItem("user_data", JSON.stringify(data.result));
    window.location.href = "app/cv-design/";
    window.location.replace();
  }
};
