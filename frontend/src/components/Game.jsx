import React, { useState, useEffect } from 'react';
import Swal from 'sweetalert2';
import { get, post } from '../api/apiService.js';
import divideTeams from '../assets/utils';

const Game = () => {
  const [teams, setTeams] = useState([]);
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
        showSuccessAlert('Éxito', 'El dinero del equipo se ha actualizado correctamente');
      }
    } catch (error) {
      showErrorAlert('Error', 'Se produjo un error al actualizar el dinero del equipo');
    }
  };

  const handleCreateGame = async () => {
    try {
      setLoading(true);
  
      // Divide los equipos antes de enviar los datos
      await handleDivideTeams();
  
      const newGameData = {
        datetime: newGameDatetime,
        location: newGameLocation,
        squad_id: selectedSquadId,
        price: price,        
        team1: teams.equipo1.join(','),
        team2: teams.equipo2.join(','),
      };
      
      console.log('Datos del nuevo partido:', newGameData);
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
      showErrorAlert('Error creating game:', error.message);
      setError(error.message);
      setLoading(false);
    }
  };

  const handleDivideTeams = async () => {
    try {
      const selectedPlayerObjects = players.filter(player => selectedPlayers.includes(player.id));
      console.log('Jugadores seleccionados:', selectedPlayerObjects);
      await setTeams(await divideTeams(selectedPlayerObjects));
      console.log('Equipo 1:', teams.equipo1);
      console.log('Equipo 2:', teams.equipo2);
    } catch (error) {
      showErrorAlert('Error dividing teams:', error.message);
    }
  };
  

  const handlePlayerSelection = (playerId) => {
    setSelectedPlayers((prevSelectedPlayers) =>
      prevSelectedPlayers.includes(playerId)
        ? prevSelectedPlayers.filter((id) => id !== playerId)
        : [...prevSelectedPlayers, playerId]
    );
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
        <div className="grid grid-cols-1 md:grid-cols-2 gap-2">
          {players.map((player) => (
            <div key={player.id} className="flex items-center">
              <input
                type="checkbox"
                id={`player-${player.id}`}
                value={player.id}
                checked={selectedPlayers.includes(player.id)}
                onChange={() => handlePlayerSelection(player.id)}
                className="mr-2"
              />
              <label htmlFor={`player-${player.id}`} className="text-lg">{player.username}</label>
            </div>
          ))}
        </div>
      </div>
      <div className="mb-4">
        <label className="block mb-1">Precio pista:</label>
        <input
          type="number"
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
      <button
        onClick={handleDivideTeams}
        className="bg-green-500 text-white font-semibold px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500 mt-2"
      >
        Dividir equipos
      </button>
      {error && <p className="text-red-500 mt-2">{error}</p>}
    </div>
  );
};

export default Game;
