import React, { useState, useEffect } from 'react';
import Swal from 'sweetalert2';
import { useParams } from 'react-router-dom';
import PlayerCard from '../components/PlayerCard.jsx';
import { get, post } from '../api/apiService';
import { RotateSpinner } from 'react-spinners-kit';

const SquadDetail = () => {
  const { id } = useParams();
  const [squad, setSquad] = useState(null);
  const [users, setUsers] = useState([]);
  const [arrStats, setArrStats] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [statsId, setStatsId] = useState('');
  const [newUserEmail, setNewUserEmail] = useState('');
  const [successMessage, setSuccessMessage] = useState('');
  const [errorMessage, setErrorMessage] = useState('');
  const [stats, setStats] = useState({
    pace: '',
    shooting: '',
    physical: '',
    defending: '',
    dribbling: '',
    passing: '',
    positions: [], 
    playerName: '',
  });

  useEffect(() => {
    const fetchSquadById = async () => {
      try {
        const data = await get(`/squad/${id}`);
        setSquad(data);
        await fetchUsersBySquadId(id);
      } catch (error) {
        setError(error);
      }
    };

    fetchSquadById();
    setTimeout(() => {
      setLoading(false);
    }, 1500);
  }, [id]);

  const fetchUsersBySquadId = async (squadId) => {
    try {
      const userData = await get(`/user/squad/${squadId}`);
      setUsers(userData);
      const statsPromises = userData.map(user => fetchStats(user.id, userData));
      const statsArray = await Promise.all(statsPromises);
      setArrStats(statsArray.filter((stat, index, self) => self.findIndex(s => s.id === stat.id) === index));
    } catch (error) {
      setError(error);
    }
  };
  
  const fetchStats = async (userId, usersData) => {
    try {
      const response = await get(`/individual-stats/user/${userId}`);
      setStatsId(response.id);
      const user = usersData.find(user => user.id === userId);
      const nombre = user ? user.username : '';
      const parsedStats = {
        ...response,
        pace: parseInt(response.pace),
        shooting: parseInt(response.shooting),
        physical: parseInt(response.physical),
        defending: parseInt(response.defending),
        dribbling: parseInt(response.dribbling),
        passing: parseInt(response.passing),
        positions: response.position ? response.position.split(',') : [],
        playerName: nombre,
      };
      return parsedStats;
    } catch (error) {
      console.error('Error fetching stats:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Hubo un error al obtener las estadísticas.',
      });
      return null;
    }
  };
  
  const handleAddUserToSquad = async () => {
    try {
      await post(`/user/${id}/addUserByEmail`, { email: newUserEmail });
      setSuccessMessage('Usuario añadido al equipo con éxito');
      setNewUserEmail('');
      await fetchUsersBySquadId(id);
    } catch (error) {
      setErrorMessage(error.message);
    }
  };

  const handleAdmin = () => {
    window.location.href = `/admin/${id}`;  
  };


  if (loading) return <p>Cargando...</p>;
  if (error) return <p>Error cargando detalles del equipo: {error.message}</p>;

  return (
    
    <>
      {loading ? (
        <div className="flex justify-center items-center h-screen">
          <RotateSpinner size={300} color="#00ff89" loading={loading} />
        </div>
      ) : (
        <div className="container mx-auto p-6 m-6 bg-white rounded-lg shadow-lg ">
        <h2 className="text-3xl font-bold mb-6">Mi Equipo</h2>
        <div className="bg-white rounded-lg shadow-lg p-6 mb-6 flex justify-evenly flex-row">
          <p className="text-xl font-semibold mt-5 mb-2">{squad.name}</p>
          <p className="text-xl font-semibold mt-5 mb-2">Saldo del equipo: x euros</p>
          
          <button
              onClick={handleAdmin}
              className="bg-blue-500 ml-3 text-white font-semibold px-6 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            Panel del administrador
          </button>
        </div>
        <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
          <h3 className="text-2xl font-bold mb-4">Miembros del equipo</h3>
          {arrStats.map(stats => (
            <div key={stats.id} className="bg-white rounded-lg shadow-lg p-6 mb-6">
              <h3 className="text-2xl font-bold mb-4">{stats.username}</h3>
              <PlayerCard {...stats} className="p-0 m-0"/>
            </div>
          ))}
          <ul>
            {users.map(user => (
              <li key={user.id} className="text-lg">{user.username}</li>
            ))}
          </ul>
        </div>
        <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
          <h3 className="text-2xl font-bold mb-4">Añadir usuario al equipo</h3>
          <input
            type="text"
            value={newUserEmail}
            onChange={(e) => setNewUserEmail(e.target.value)}
            placeholder="usuario@ejemplo.com"
            className="border border-gray-300 rounded-lg px-4 py-2 mb-4"
          />
          <button
            onClick={handleAddUserToSquad}
            className="bg-blue-500 ml-3 text-white font-semibold px-6 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            Añadir
          </button>
          {successMessage && <p className="text-green-500 mt-2">Usuario añadido a su equipo</p>}
          {/* {errorMessage && <p className="text-red-500 mt-2">{errorMessage}</p>} */}
        </div>
      </div>
      )}
    </>
  );
};

export default SquadDetail;