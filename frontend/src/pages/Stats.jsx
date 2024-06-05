import React, { useState, useEffect } from 'react';
import Swal from 'sweetalert2';
import { post, get } from '../api/apiService.js'; // Importamos las funciones put y get
import PlayerCard from '../components/PlayerCard.jsx';

const Stats = () => {
  const userId = sessionStorage.getItem('userId');
  const [statsId, setStatsId] = useState(null);
  const [stats, setStats] = useState({
    id: '', // Agregamos el ID aquí
    pace: '',
    shooting: '',
    physical: '',
    defending: '',
    dribbling: '',
    passing: '',
    positions: []
  });

  useEffect(() => {
    const fetchStats = async () => {
      try {
        const response = await get(`/individual-stats/user/${userId}`);
        setStatsId(response.id);
        const parsedStats = {
          ...response,
          pace: parseInt(response.pace),
          shooting: parseInt(response.shooting),
          physical: parseInt(response.physical),
          defending: parseInt(response.defending),
          dribbling: parseInt(response.dribbling),
          passing: parseInt(response.passing),
          positions: response.position ? response.position.split(',') : []
        };
        setStats(parsedStats);
      } catch (error) {
        console.error('Error fetching stats:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'There was an error fetching stats!',
        });
      }
    };

    fetchStats();
  }, [userId]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setStats(prevStats => ({
      ...prevStats,
      [name]: value
    }));
  };

  const handlePositionChange = (e) => {
    const { value, checked } = e.target;
    setStats(prevStats => {
      if (checked) {
        return { ...prevStats, positions: [...prevStats.positions, value] };
      } else {
        return { ...prevStats, positions: prevStats.positions.filter(pos => pos !== value) };
      }
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (
        parseInt(stats.pace) === 0 ||
        parseInt(stats.shooting) === 0 ||
        parseInt(stats.physical) === 0 ||
        parseInt(stats.defending) === 0 ||
        parseInt(stats.dribbling) === 0 ||
        parseInt(stats.passing) === 0
      ) {
        throw new Error('Los campos no pueden tener un valor de 0.');
      }
      const digits = ['pace', 'shooting', 'physical', 'defending', 'dribbling', 'passing'];
      digits.forEach(key => {
        if (stats[key].length > 2) {
          throw new Error(`Los campos solo pueden tener un máximo de dos dígitos: ${key}`);
        }
      });

      const response = await post(`/individual-stats/${statsId}`, {
        ...stats,
        user_id: userId,
        position: stats.positions.join(',')
      });

      // Actualizamos stats con los datos actualizados desde el servidor
      setStats(prevStats => ({
        ...prevStats,
        ...response
      }));

      Swal.fire({
        icon: 'success',
        title: 'Stats Updated',
        text: 'Stats actualizadas correctamente!',
      });
    } catch (error) {
      console.error('Error updating stats:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: error.message || 'There was an error updating the stats!',
      });
    }
  };


  return (
    <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', height: '100vh' }}>
      <PlayerCard {...stats} className="p-0 m-0"/>
    <div className=" max-w-lg mx-auto p-6 bg-white rounded-lg shadow-md">
      <h2 className="text-2xl font-bold mb-4">Actualizar tus Estadísticas Individuales</h2>
      <form onSubmit={handleSubmit} className="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div className="flex flex-col">
          <label className="mb-2">Ritmo:</label>
          <input type="number" name="pace" placeholder="Ritmo" value={stats.pace} onChange={handleChange} className="p-2 border rounded" />
        </div>
        <div className="flex flex-col">
          <label className="mb-2">Tiro:</label>
          <input type="number" name="shooting" placeholder="Tiro" value={stats.shooting} onChange={handleChange} className="p-2 border rounded" />
        </div>
        <div className="flex flex-col">
          <label className="mb-2">Físico:</label>
          <input type="number" name="physical" placeholder="Físico" value={stats.physical} onChange={handleChange} className="p-2 border rounded" />
        </div>
        <div className="flex flex-col">
          <label className="mb-2">Defensa:</label>
          <input type="number" name="defending" placeholder="Defensa" value={stats.defending} onChange={handleChange} className="p-2 border rounded" />
        </div>
        <div className="flex flex-col">
          <label className="mb-2">Regate:</label>
          <input type="number" name="dribbling" placeholder="Regate" value={stats.dribbling} onChange={handleChange} className="p-2 border rounded" />
        </div>
        <div className="flex flex-col">
          <label className="mb-2">Pase:</label>
          <input type="number" name="passing" placeholder="Pase" value={stats.passing} onChange={handleChange} className="p-2 border rounded" />
        </div>
        <div className="flex flex-col">
          <label className="mb-2">Posición:</label>
          <label className="inline-flex items-center">
            <input type="checkbox" name="position" value="PT" checked={stats.positions.includes("PT")} onChange={handlePositionChange} className="mr-1" />
            PT - Portero
          </label>
          <label className="inline-flex items-center">
            <input type="checkbox" name="position" value="DF" checked={stats.positions.includes("DF")} onChange={handlePositionChange} className="mr-1" />
            DF - Defensa
          </label>
          <label className="inline-flex items-center">
            <input type="checkbox" name="position" value="MC" checked={stats.positions.includes("MC")} onChange={handlePositionChange} className="mr-1" />
            MC - Centrocampista
          </label>
          <label className="inline-flex items-center">
            <input type="checkbox" name="position" value="DC" checked={stats.positions.includes("DC")} onChange={handlePositionChange} className="mr-1" />
            DC - Delantero
          </label>
        </div>
        <button type="submit" className="col-span-2 p-2 bg-blue-500 text-white rounded hover:bg-blue-600">Actualizar Estadísticas</button>
      </form>
    </div>
      
      </div>
  );
  
};

export default Stats;