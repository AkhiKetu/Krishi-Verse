const API_KEY = "YOUR_API_KEY_HERE"; // Replace this with your actual OpenWeatherMap API key

// Dhaka as default location
const LAT = 23.8103;
const LON = 90.4125;

document.addEventListener("DOMContentLoaded", () => {
  fetch(`https://api.openweathermap.org/data/2.5/onecall?lat=${LAT}&lon=${LON}&exclude=minutely,hourly&units=metric&appid=${API_KEY}`)
    .then(res => res.json())
    .then(data => renderWeather(data))
    .catch(err => {
      document.getElementById("weather-section").innerHTML = "<p>Error loading weather data.</p>";
      console.error(err);
    });
});

function renderWeather(data) {
  document.getElementById("temperature").textContent = `${Math.round(data.current.temp)}°C`;
  document.getElementById("feels-like").textContent = `Feels like ${Math.round(data.current.feels_like)}°C`;
  document.getElementById("description").textContent = capitalize(data.current.weather[0].description);
  document.getElementById("humidity").textContent = `${data.current.humidity}%`;
  document.getElementById("wind").textContent = `${data.current.wind_speed} km/h`;
  document.getElementById("visibility").textContent = `${data.current.visibility / 1000} km`;
  document.getElementById("pressure").textContent = `${data.current.pressure} hPa`;
  document.getElementById("uv").textContent = data.current.uvi;

  const forecastList = document.getElementById("forecast-list");
  forecastList.innerHTML = "";
  data.daily.slice(0, 5).forEach((day, i) => {
    const d = new Date(day.dt * 1000);
    const dayName = i === 0 ? "Today" : d.toLocaleDateString("en-US", { weekday: "long" });
    const pop = Math.round(day.pop * 100);
    forecastList.innerHTML += `
      <div class="forecast-item">
        <strong>${dayName}</strong>
        <div>${capitalize(day.weather[0].description)}</div>
        <div>${Math.round(day.temp.day)}° / ${Math.round(day.temp.night)}°</div>
        <div>${pop}% rain</div>
      </div>`;
  });

  const alertsList = document.getElementById("alerts-list");
  alertsList.innerHTML = "";
  if (data.alerts) {
    data.alerts.forEach(alert => {
      const level = alert.event.toLowerCase().includes("warning") ? "high" : "medium";
      const endDate = new Date(alert.end * 1000).toLocaleDateString("en-US");
      alertsList.innerHTML += `
        <div class="alert-box ${level}">
          <strong>${alert.event}</strong> <span class="level">${capitalize(level)}</span>
          <p>${alert.description}</p>
          <small>Valid until: ${endDate}</small><br>
          <small>Sender: ${alert.sender_name || "N/A"}</small>
        </div>`;
    });
  } else {
    alertsList.innerHTML = "<p>No weather alerts currently.</p>";
  }
}

function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}
