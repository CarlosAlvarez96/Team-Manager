import React, { useState, useEffect } from 'react';
import Swal from 'sweetalert2';
import { get, post } from '../api/apiService.js';

const Game = () => {
  const [games, setGames] = useState([]);
  const [squads, setSquads] = useState([]);
  const [players, setPlayers] = useState([]);
  const [selectedSquadId, setSelectedSquadId] = useState(null);
  const [newGameLocation, setNewGameLocation] = useState('');
  const [price, setPrice] = useState('');
  const [newGameDatetime, setNewGameDatetime] = useState('');
  const [selectedPlayers, setSelectedPlayers] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchSquads = async () => {
      try {
        const squadsData = await get('/squad/all');
        setSquads(squadsData);
      } catch (error) {
        console.error('Error fetching squads:', error);
        showErrorAlert('Error fetching squads:', error.message);
      }
    };

    fetchSquads();
  }, []);

  useEffect(() => {
    const fetchPlayers = async () => {
      if (selectedSquadId) {
        try {
          const playersData = await get(`/user/squad/${selectedSquadId}`);
          setPlayers(playersData);
        } catch (error) {
          console.error('Error fetching players:', error);
          showErrorAlert('Error fetching players:', error.message);
        }
      }
    };

    fetchPlayers();
  }, [selectedSquadId]);

  const showErrorAlert = (title, message) => {
    Swal.fire({
      icon: 'error',
      title: title,
      text: message,
    });
  };

  const showSuccessAlert = (title, message) => {
    Swal.fire({
      icon: 'success',
      title: title,
      text: message,
    });
  };

  const handleMoneyUpdate = async () => {
    try {
      const squad = await get(`/squad/${selectedSquadId}`);
      if (squad.money < price) {
        showErrorAlert('Error', 'No tienes suficiente dinero en el equipo para jugar este partido');
      } else {
        const newBalance = squad.money - price;
        await post(`/squad/${selectedSquadId}/updateMoney`, { money: newBalance });
        Swal.fire({
          icon: 'success',
          title: 'Éxito',
          text: 'El dinero del equipo se ha actualizado correctamente',
        });
      }
    } catch (error) {
      showErrorAlert('Error', 'Se produjo un error al actualizar el dinero del equipo');
    }
  };

  const handleCreateGame = async () => {
    try {
      setLoading(true);

      const newGameData = {
        datetime: newGameDatetime,
        location: newGameLocation,
        squad_id: selectedSquadId,
        players: selectedPlayers,
      };

      await post('/game/create', newGameData);

      setLoading(false);
      setError(null);
      setNewGameLocation('');
      setNewGameDatetime('');
      setSelectedSquadId(null);
      setSelectedPlayers([]);
      handleMoneyUpdate();
      showSuccessAlert('¡Partido creado con éxito!', '');
    } catch (error) {
      setError(error.message);
      setLoading(false);
      showErrorAlert('Error creating game:', error.message);
    }
  };

  return (
    <div className="container bg-white p-5 rounded-md m-10 mx-auto">
      <h2 className="text-2xl font-bold mb-4">Jugar un partido</h2>
      <div className="mb-4">
        <label htmlFor="location" className="block mb-1">Lugar:</label>
        <input
          type="text"
          id="location"
          value={newGameLocation}
          onChange={(e) => setNewGameLocation(e.target.value)}
          className="border border-gray-300 rounded-md px-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>
      <div className="mb-4">
        <label htmlFor="datetime" className="block mb-1">Fecha y hora:</label>
        <input
          type="datetime-local"
          id="datetime"
          value={newGameDatetime}
          onChange={(e) => setNewGameDatetime(e.target.value)}
          className="border border-gray-300 rounded-md px-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>
      <div className="mb-4">
        <label htmlFor="squad" className="block mb-1">Equipo:</label>
        <select
          id="squad"
          value={selectedSquadId}
          onChange={(e) => setSelectedSquadId(e.target.value)}
          className="border border-gray-300 rounded-md px-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Seleccione un equipo</option>
          {squads.map((squad) => (
            <option key={squad.id} value={squad.id}>{squad.name}</option>
          ))}
        </select>
      </div>
      <div className="mb-4">
        <label className="block mb-1">Jugadores:</label>
        <select
          multiple
          value={selectedPlayers}
          onChange={(e) => setSelectedPlayers(Array.from(e.target.selectedOptions, option => option.value))}
          className="border border-gray-300 rounded-md px-4 py-2 w-full h-32 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          {players.map((player) => (
            <option key={player.id} value={player.id}>{player.username}</option>
          ))}
        </select>
      </div>
      <div className="mb-4">
        <label className="block mb-1">Precio pista:</label>
        <input type="number"
          value={price}
          onChange={(e) => setPrice(e.target.value)}
          className="border border-gray-300 rounded-md px-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>
      <button
        onClick={handleCreateGame}
        className="bg-blue-500 text-white font-semibold px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
        disabled={loading}
      >
        {loading ? 'Creating Game...' : 'Create Game'}
      </button>
      {error && <p className="text-red-500 mt-2">{error}</p>}
    </div>
  );
};

export default Game;