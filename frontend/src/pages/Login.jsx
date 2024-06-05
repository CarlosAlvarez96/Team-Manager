import { useState, useEffect } from 'react';
import { useAuth } from '../context/AuthContext';
import { useNavigate } from 'react-router-dom';
import Swal from 'sweetalert2';

const Login = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const { login, setEmail: setAuthEmail } = useAuth();
  const navigate = useNavigate();

  // Cargar datos de inicio de sesión desde el almacenamiento local cuando el componente se monta
  useEffect(() => {
    const storedEmail = localStorage.getItem('email');
    if (storedEmail) {
      setEmail(storedEmail);
    }
  }, []);

  const handleSubmit = async (e) => {
    e.preventDefault();

    try {
      const response = await fetch('http://localhost/api/login_check', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password }),
      });

      if (!response.ok) {
        throw new Error('Login failed');
      }

      const data = await response.json();
      const accessToken = data.token;

      login(accessToken, email, password, email);

      const userResponse = await fetch('http://localhost/user/me', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });

      const userData = await userResponse.json();
      const userEmail = userData.email || 'default@example.com';
      const userId = userData.id;
      setAuthEmail(userEmail);

      // Guardar email y userId en el almacenamiento local
      localStorage.setItem('email', userEmail);
      sessionStorage.setItem('userId', userId);

      Swal.fire({
        icon: 'success',
        title: 'Login successful',
        text: `Welcome, ${userData.email || 'User'}!`,
      });

      navigate('/');
    } catch (error) {
      console.error('Login failed', error);
      Swal.fire({
        icon: 'error',
        title: 'Login Failed',
        text: 'Invalid credentials',
      });
    }
  };

  return (
    <div className="mt-20">
      <form className="max-w-sm mx-auto" onSubmit={handleSubmit}>
        <h2 className="block mb-4 text-2xl font-medium">Sign in</h2>
        <hr className="mb-10" />
        <div className="mb-5">
          <label htmlFor="email" className="block mb-2 text-sm font-medium">
            Email
          </label>
          <input
            type="email"
            id="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
            placeholder="example@example.com"
            required
          />
        </div>

        <div className="mb-5">
          <label htmlFor="password" className="block mb-2 text-sm font-medium">
            Password
          </label>
          <input
            type="password"
            id="password"
            value={password}
            placeholder="********"
            onChange={(e) => setPassword(e.target.value)}
            className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
            required
          />
        </div>
        <button
          type="submit"
          className="bg-gray-300 hover:bg-gray-500 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center"
        >
          Submit
        </button>
      </form>
    </div>
  );
};

export default Login;
