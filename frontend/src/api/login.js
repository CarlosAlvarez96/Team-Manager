import axios from 'axios';

// Function to make a GET request to fetch users by URL
const getUsers = (url) => {
  axios.get(url)
    .then(response => {
      // Handle the response data
      console.log(response.data);
    })
    .catch(error => {
      // Handle any errors
      console.error(error);
    });
};

// Function to make a POST request to register a new user with URL and data
const registerUser = (url, data) => {
  axios.post(url, data)
    .then(response => {
      // Handle the response data
      console.log(response.data);
    })
    .catch(error => {
      // Handle any errors
      console.error(error);
    });
};


// Usage examples
getUsers('/user/all'); // Fetch all users
getUsers('/user/{id}'); // Fetch a specific user by ID
registerUser('/user/register', {
  email: 'example@example.com',
  password: 'password123'
}); // Register a new user
