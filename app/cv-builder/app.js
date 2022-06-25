const acc = document.getElementsByClassName("accordion");

for (let i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function () {
    // this.classList.toggle("active");
    const panel = this.nextElementSibling;
    if (panel.style.display === "block") {
      panel.style.display = "none";
    } else {
      panel.style.display = "block";
    }
  });
}

const showStick = (error) => {
  const stick = document.getElementsByClassName("stick");
  stick[0].style.display = "block";
  stick[0].innerHTML = `<p>${error}</p>`;
  setTimeout(() => {
    stick[0].style.display = "none";
    stick[0].innerHTML = ``;
  }, 5000);
};

const workDetailHtml = (val) => {
  return `
<div class="cv-block__values">
  <table>
    <tr>
      <td><label for="cp-name-${val}">Company Name</label></td>
      <td>
        <input type="text" name="cp-name" id="cp-name-${val}" placeholder="Microsoft" />
      </td>
    </tr>
    <tr>
      <td><label for="cd-${val}">Current Designation</label></td>
      <td>
        <input type="text" name="cd" id="cd-${val}" placeholder="Software Engineer" />
      </td>
    </tr>
    <tr>
      <td><label for="tye-${val}">Total Years of Experience</label></td>
      <td>
        <input type="number" name="tye" id="tye-${val}" placeholder="2" />
      </td>
    </tr>
    <tr>
      <td><label for="dwe-${val}">Details of Work Experience</label></td>
      <td>
        <textarea name="dwe" id="dwe-${val}" rows="4"></textarea>
      </td>
    </tr>
  </table>
</div>
<div class="cv-icons">
<button class="cv-icons__icon" onclick="clearWorkDetails(event,'${val}')">
  <img src="../../static/icons/erase.svg" alt="clear" />&nbsp;Clear
</button>
<button class="cv-icons__icon" onclick="deleteWorkDetails(event,'${val}')">
  <img src="../../static/icons/trash.svg" alt="delete" />&nbsp;Delete
</button>
</div>
`;
};

const eduDetailHtml = (val) => {
  return `
<div class="cv-block__values cv-edu__override">
<table>
  <tr>
    <td><label for="dp-${val}">Degree Program</label></td>
    <td>
      <input
        type="text"
        name="dp"
        id="dp-${val}"
        placeholder="Bachelor's of Computer"
      />
    </td>
  </tr>
  <tr>
    <td><label for="dy-${val}">Degree Year</label></td>
    <td>
      <input
        type="number"
        name="dy"
        id="dy-${val}"
        placeholder="2022"
      />
    </td>
  </tr>
  <tr>
    <td>
      <label for="om-${val}">Obtained Marks</label>
    </td>
    <td>
      <input
        type="number"
        name="om"
        id="om-${val}"
        placeholder="89"
      />
    </td>
  </tr>
</table>
</div>
<div class="cv-icons">
<button class="cv-icons__icon" onclick="clearEduDetails(event,'${val}')">
  <img src="../../static/icons/erase.svg" alt="clear" />&nbsp;Clear
</button>
<button class="cv-icons__icon" onclick="deleteEduDetails(event,'${val}')">
  <img src="../../static/icons/trash.svg" alt="delete" />&nbsp;Delete
</button>
</div>
`;
};

const detailGen = (val, html, id) => {
  const topDiv = document.createElement("div");
  topDiv.className = "cv-details__values";
  topDiv.style = "margin-top: 5px;";
  topDiv.id = `${id}-${val}`;
  topDiv.innerHTML = html;
  return topDiv;
};

let detailCount = 0;
const addWorkDetailForm = (event) => {
  event.preventDefault();
  const workPlaceholder = document.getElementById("work-placeholder");
  const hr = document.createElement("hr");
  hr.id = `wdghr-${detailCount}`;
  workPlaceholder.appendChild(hr);
  workPlaceholder.appendChild(
    detailGen(detailCount, workDetailHtml(detailCount), "wdg")
  );
  detailCount++;
};
const addEduDetailForm = (event) => {
  event.preventDefault();
  const workPlaceholder = document.getElementById("edu-placeholder");
  const hr = document.createElement("hr");
  hr.id = `edghr-${detailCount}`;
  workPlaceholder.appendChild(hr);
  workPlaceholder.appendChild(
    detailGen(detailCount, eduDetailHtml(detailCount), "edg")
  );
  detailCount++;
};

const clearWorkDetails = (event, val) => {
  event.preventDefault();
  document.getElementById(`${val == "" ? "cp-name" : "cp-name-" + val}`).value =
    "";
  document.getElementById(`${val == "" ? "cd" : "cd-" + val}`).value = "";
  document.getElementById(`${val == "" ? "tye" : "tye-" + val}`).value = "";
  document.getElementById(`${val == "" ? "dwe" : "dwe-" + val}`).value = "";
};
const clearEduDetails = (event, val) => {
  event.preventDefault();
  document.getElementById(`${val == "" ? "dp" : "dp-" + val}`).value = "";
  document.getElementById(`${val == "" ? "dy" : "dy-" + val}`).value = "";
  document.getElementById(`${val == "" ? "om" : "om-" + val}`).value = "";
};

const deleteWorkDetails = (event, val) => {
  event.preventDefault();
  document.getElementById(`wdghr-${val}`).remove();
  document.getElementById(`wdg-${val}`).remove();
};
const deleteEduDetails = (event, val) => {
  event.preventDefault();
  document.getElementById(`edghr-${val}`).remove();
  document.getElementById(`edg-${val}`).remove();
};

const getPersonalDetails = () => {
  return [
    document.getElementById("name").value,
    document.getElementById("phone").value,
    document.getElementById("email").value,
    Array.from(document.getElementsByName("gender")).find(
      (radio) => radio.checked
    ).value,
    document.getElementById("dob").value,
    document.getElementById("add").value,
  ];
};
const getWorkDetails = () => {
  const workDetails = [];
  const cp_name = document.getElementsByName("cp-name");
  const cd = document.getElementsByName("cd");
  const tye = document.getElementsByName("tye");
  const dwe = document.getElementsByName("dwe");
  for (let i = 0; i < cp_name.length; i++) {
    workDetails.push([
      cp_name[i].value,
      cd[i].value,
      tye[i].value,
      dwe[i].value,
    ]);
  }
  return workDetails;
};
const getEduDetails = () => {
  const workDetails = [];
  const dp = document.getElementsByName("dp");
  const dy = document.getElementsByName("dy");
  const om = document.getElementsByName("om");
  for (let i = 0; i < dp.length; i++) {
    workDetails.push([dp[i].value, dy[i].value, om[i].value]);
  }
  return workDetails;
};
const getCredDetails = () => {
  return [
    document.getElementById("username").value,
    document.getElementById("password").value,
  ];
};

const submitCv = async (event) => {
  event.preventDefault();

  const submitObj = {
    personalDetails: getPersonalDetails(),
    workDetails: getWorkDetails(),
    eduDetails: getEduDetails(),
    credDetails: getCredDetails(),
  };

  const form_data = new FormData();
  const my_file = document.getElementById("input-pic").files[0];
  if (typeof my_file == "undefined") {
    showStick("Please Upload your picture");
  } else {
    const new_file_name =
      my_file.name
        .split(".")[0]
        .replaceAll(":", "")
        .replaceAll("-", "_")
        .replaceAll(" ", "_") +
      "_" +
      new Date()
        .toISOString()
        .split(".")[0]
        .replaceAll(":", "")
        .replaceAll("-", "_") +
      "." +
      my_file.name.split(".")[my_file.name.split(".").length - 1];
    form_data.append("file", my_file, new_file_name);
    form_data.append("my_data", JSON.stringify(submitObj));

    const response = await fetch(
      `${window.location.pathname.split("app")[0]}server/create-cv.php`,
      {
        method: "post",
        body: form_data,
      }
    );
    const data = await response.json();
    if (data.error.length > 0) {
      showStick(data.error[0]);
    } else {
      sessionStorage.removeItem("user_data");
      sessionStorage.setItem("user_data", JSON.stringify(data.result));
      window.location.href = `${window.location.pathname.split("app")[0]}app/cv-design/`;
    }
  }
};

let picInput = document.getElementById("input-pic");
let imageName = document.getElementById("input-pic__text");
picInput.addEventListener("change", () => {
  let inputImage = document.querySelector("input[type=file]").files[0];
  imageName.innerText = inputImage.name;
});

const goHome = () => {
  window.location.href = window.location.pathname.split("app")[0];
};
