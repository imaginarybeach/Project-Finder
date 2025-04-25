// In your frontend JavaScript
fetch('db.php')
  .then(response => response.json())  // If your PHP returns JSON
  .then(data => {
    console.log(data);
    // Update your UI with the data
    displayStudentData(data);
  })
  .catch(error => console.error('Error:', error));

function displayStudentData(data) {
  // Update your UI with the received data
  const container = document.getElementById('student-container');
  data.forEach(student => {
    container.innerHTML += `<div>${student.id} - ${student.email}</div>`;
  });
}
