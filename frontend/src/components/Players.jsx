import React, { useState, useEffect } from 'react';
import PlayerCard from '../components/PlayerCard.jsx';
import { get } from '../api/apiService';

const Players = ({ squadId }) => {
  const [players, setPlayers] = useState([]);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchPlayers = async () => {
      try {
        const userData = await get(`/user/squad/${squadId}`);
        const statsPromises = userData.map(user => fetchStats(user.id));
        const statsArray = await Promise.all(statsPromises);
        setPlayers(statsArray);
      } catch (error) {
        setError(error);
      }
    };

    fetchPlayers();
  }, [squadId]);

  const fetchStats = async (userId) => {
    try {
      const response = await get(`/individual-stats/user/${userId}`);
      return {
        id: userId,
        ...response,
      };
    } catch (error) {
      console.error('Error fetching stats:', error);
      return null;
    }
  };

  if (error) return <p>Error cargando jugadores: {error.message}</p>;

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      {players.map(player => (
        <PlayerCard key={player.id} {...player} />
      ))}
    </div>
  );
};

export default Players;
