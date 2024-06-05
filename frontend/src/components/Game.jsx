import React, { useState, useEffect } from 'react';
import Swal from 'sweetalert2';
import { get, post } from '../api/apiService.js';

const Game = () => {
  const [games, setGames] = useState([]);
  const [squads, setSquads] = useState([]);
  const [selectedSquadId, setSelectedSquadId] = useState(null);
  const [newGameLocation, setNewGameLocation] = useState('');
  const [newGameDatetime, setNewGameDatetime] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchGames = async () => {
      try {
        setLoading(true);
        const gamesData = await get('/game/all');
        setGames(gamesData);
      } catch (error) {
        setError(error.message);
        showErrorAlert('Error fetching games:', error.message);
      } finally {
        setLoading(false);
      }
    };

    const fetchSquads = async () => {
      try {
        const squadsData = await get('/squad/all');
        setSquads(squadsData);
      } catch (error) {
        console.error('Error fetching squads:', error);
        showErrorAlert('Error fetching squads:', error.message);
      }
    };

    fetchGames();
    fetchSquads();
  }, []);

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

  const handleCreateGame = async () => {
    try {
      setLoading(true);

      // Create game data object
      const newGameData = {
        datetime: newGameDatetime,
        location: newGameLocation,
        squad_id: selectedSquadId,
      };

      // Post new game data to backend
      await post('/game/create', newGameData);

      // Reset state and show success message
      setLoading(false);
      setError(null);
      setNewGameLocation('');
      setNewGameDatetime('');
      setSelectedSquadId(null);
      showSuccessAlert('Game created successfully!', '');
    } catch (error) {
      console.error('Error creating game:', error);
      setError(error.message);
      setLoading(false);
      showErrorAlert('Error creating game:', error.message);
    }
  };

  return (
    <div className="container bg-white p-5 rounded-md m-10 mx-auto">
      <h2 className="text-2xl font-bold mb-4">Create a New Game</h2>
      <div className="mb-4">
        <label htmlFor="location" className="block mb-1">Lugar:</label>
        <input
          type="text"
          id="location"
          value={newGameLocation}
          onChange={(e) => setNewGameLocation(e.target.value)}
          className="border border-gray-300 rounded-md px-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <p className="text-xs text-gray-500"></p>
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
        <label htmlFor="squad" className="block mb-1">Escuadrón:</label>
        <select
          id="squad"
          value={selectedSquadId}
          onChange={(e) => setSelectedSquadId(e.target.value)}
          className="border border-gray-300 rounded-md px-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Seleccione un escuadrón</option>
          {squads.map((squad) => (
            <option key={squad.id} value={squad.id}>{squad.name}</option>
          ))}
        </select>
      </div>
      <button
        onClick={handleCreateGame}
        className="bg-blue-500 text-white font-semibold px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
        disabled={loading}
      >
        {loading ? 'Creating Game...' : 'Create Game'}
      </button>
      {error && <p className="text-red-500 mt-2">{error}</p>}

      <div className="mt-8">
        <h2 className="text-2xl font-bold mb-4">Games</h2>
        {loading && <p>Loading games...</p>}
        {!loading && games.length === 0 && <p>No games found.</p>}
        {!loading && games.length > 0 && (
          <ul>
            {games.map((game) => (
              <li key={game.id}>
                <strong>Lugar:</strong> {game.location}<br />
                <strong>Date:</strong> {game.datetime}<br />
                {/* <strong>Squad:</strong> {game.squad.name}<br /> */}
              </li>
            ))}
          </ul>
        )}
      </div>
    </div>
  );
};

export default Game;
