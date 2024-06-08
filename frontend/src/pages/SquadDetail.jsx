import React, { useState, useEffect } from 'react';
import Swal from 'sweetalert2';
import { useParams } from 'react-router-dom';
import PlayerCard from '../components/PlayerCard.jsx';
import { get, post } from '../api/apiService';

const SquadDetail = () => {
  const { id } = useParams();
  const [squad, setSquad] = useState(null);
  const [games, setGames] = useState([]);
  const [users, setUsers] = useState([]);
  const [arrStats, setArrStats] = useState([]);
  const [error, setError] = useState(null);
  const [statsId, setStatsId] = useState('');
  const [newUserEmail, setNewUserEmail] = useState('');
  const [successMessage, setSuccessMessage] = useState('');
  const [errorMessage, setErrorMessage] = useState('');
  const [newMoney, setNewMoney] = useState('');
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

  const currentUserId = parseInt(sessionStorage.getItem('userId')); // Reemplaza esto con la forma en que obtienes el ID del usuario actual
  console.log('Current User ID:', currentUserId); // Para verificar que el ID del usuario actual se obtiene correctamente
  const fetchSquadById = async () => {
    try {
      const data = await get(`/squad/${id}`);
      setSquad(data);
      await fetchUsersBySquadId(id);
    } catch (error) {
      setError(error);
    }
  };

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

  const handleMoneyUpdate = async () => {
    try {
      await post(`/squad/${squad.id}/updateMoney`, { money: newMoney });
      await fetchSquadById(); // Volver a cargar los detalles del equipo después de actualizar el dinero
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Squad money updated successfully',
      });
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'An error occurred while updating squad money',
      });
    }
  };

  useEffect(() => {
    const fetchSquadById = async () => {
      try {
        const data = await get(`/squad/${id}`);
        console.log('Squad Data:', data); // Para verificar la estructura de la respuesta
        setSquad(data);
        await fetchUsersBySquadId(id);
      } catch (error) {
        setError(error);
      }
    }
    const fetchGames = async (squadId) => {
      try {
        const gamesData = await post('/game/' + squadId);
        setGames(gamesData);
      } catch (error) {
        setError(error.message);
        showErrorAlert('Error fetching games:', error.message);
      } 
    };

    fetchGames(id);
    fetchSquadById();
  }, [id]);

  const fetchStats = async (userId, usersData) => {
    try {
      const response = await get(`/individual-stats/user/${userId}`);
      const user = usersData.find(user => user.id === userId);
      const nombre = user ? user.username : '';
      const parsedStats = {
        ...response,
        playerName: nombre,
        rating: calculateRating(response),
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

  const calculateRating = (stats) => {
    return Math.round((stats.pace + stats.shooting + stats.physical + stats.defending + stats.dribbling + stats.passing) / 6);
  };

  if (error) return <p>Error cargando detalles del equipo: {error.message}</p>;

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

  const handleRemoveSelectedPlayers = async (userId) => {
    try {
      await delete(`/${id}/removeUser/${userId}`);
      showSuccessAlert('Success', 'Player deleted successfully');
      await fetchUsersBySquadId(id);
    } catch (error) {
      showErrorAlert('Error', 'Failed to delete player');
    }
  };

  if (error) return <p>Error cargando detalles del equipo: {error.message}</p>;

  return (
    <div className="container mx-auto p-6 m-6 bg-white rounded-lg shadow-lg">
      <h2 className="text-3xl font-bold mb-6">Mi Equipo</h2>
      {squad ? (
        <div>
          <div className="bg-white rounded-lg shadow-lg p-6 mb-6 flex justify-evenly flex-row">
            <p className="text-xl font-semibold mt-5 mb-2">{squad.name}</p>
            <div className="mt-4">
              <h2 className="text-xl font-semibold mb-2">Actualizar el saldo</h2>
              <div className="flex items-center space-x-4 mb-4">
                <input
                  type="text"
                  value={newMoney}
                  onChange={(e) => setNewMoney(e.target.value)}
                  placeholder="Ejemplo: 50"
                  className="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <button
                  onClick={handleMoneyUpdate}
                  className="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  Actualizar
                </button>
              </div>
            </div>
            <p className="text-xl font-semibold mt-5 mb-2">Saldo del equipo: <span className="text-green-600">{squad.money} euros</span></p>
          </div>
          <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 className="text-2xl font-bold mb-4">Miembros del equipo</h3>
            {arrStats.map(stats => (
              <div key={stats.id} className="bg-white rounded-lg shadow-lg p-6 mb-6 flex flex-row">
                <h3 className="text-2xl font-bold mb-4">{stats.username}</h3>
                <PlayerCard {...stats} className="p-0 m-0"/>
                <div className="ml-6 p-auto m-auto">
                  <p className="font-bold">PAC = Rítmo, Aceleracion, Velocidad: {stats.pace}</p>
                  <p className="font-bold">SHO = Tiro, Remate: {stats.shooting}</p>
                  <p className="font-bold">PHY = Fuerza, resistencia: {stats.physical}</p>
                  <p className="font-bold">DEF = Tackle, contención, intercepciones: {stats.defending}</p>
                  <p className="font-bold">DRI = Regate, dribbling: {stats.dribbling}</p>
                  <p className="font-bold">PAS = Pase, centro, pase largo: {stats.passing}</p>
                  <p className="font-bold">Posición/es: {stats.position}</p>
                  {squad.manager === currentUserId && (
                    <button
                      onClick={() => handleRemoveSelectedPlayers(stats.user_id)}
                      className="bg-red-500 text-white font-semibold px-2 py-2 m-20 rounded focus:outline-none focus:ring-2 focus:ring-red-500 mr-2"
                    >
                      Quitar del equipo
                    </button>
                  )}
                </div>
              </div>
            ))}
          </div>
        </div>
      ) : (
        <p>Cargando detalles del equipo...</p>
      )}
      {squad && squad.manager === currentUserId && (
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
          {errorMessage && <p className="text-red-500 mt-2">{errorMessage}</p>}
        </div>
      )}
      <div className="mt-8">
        <h2 className="text-2xl font-bold mb-4">Partidos jugados</h2>
        {games.length === 0 && <p>No se encontraron partidos.</p>}
        {games.length > 0 && (
          <ul>
            {games.map((game) => (
              <li key={game.id}>
                <strong>Lugar:</strong> {game.location}<br />
                <strong>Date:</strong> {game.datetime}<br />
              </li>
            ))}
          </ul>
        )}
      </div>
    </div>
  );
};

export default SquadDetail;
