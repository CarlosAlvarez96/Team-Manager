import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { get, post, deleteMethod } from '../api/apiService.js';
import '../Squad.css';
import { RotateSpinner } from 'react-spinners-kit';

const Squad = () => {
  const [squads, setSquads] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [newSquadName, setNewSquadName] = useState('');

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await get('/user/me');
        const userId = response.id;
        const data = await get(`/squad/squads/${userId}`);
        setSquads(data);
        setTimeout(() => {
          setLoading(false);
        }, 1500);
      } catch (error) {
        setError(error);
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  const handleCreateSquad = async () => {
    try {
      const response = await get('/user/me');
      const userId = response.id;
      const newSquadResponse = await post(`/squad/create/${userId}`, { name: newSquadName });
      const { squadId } = newSquadResponse;
      await post(`/squad/${squadId}/addUser/${userId}`);
      const newData = await get(`/squad/squads/${userId}`);
      setSquads(newData);
      setNewSquadName('');
    } catch (error) {
      setError(error);
    }
  };

  const handleDeleteSquad = async (squadId) => {
    try {
      await deleteMethod(`/squad/${squadId}`);
      const response = await get('/user/me');
      const userId = response.id;
      const newData = await get(`/squad/squads/${userId}`);
      setSquads(newData);
    } catch (error) {
      setError(error);
    }
  };

  return (
    <>
      {loading ? (
        <div className="flex justify-center items-center h-screen">
          <RotateSpinner size={300} color="#00ff89" loading={loading} />
        </div>
      ) : (
        <div className="container bg-white p-5 rounded-md m-10 mx-auto">
          <h2 className="text-2xl font-bold mb-4">Tus peña</h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            {squads.map((squad) => (
              <div key={squad.id} className="bg-white shadow-lg rounded-lg overflow-hidden">
                <div className="p-4 flex justify-between items-center">
                  <h3 className="text-lg font-bold mb-2">{squad.name}</h3>
                  <div className="flex">
                    <Link to={`/squaddetail/${squad.id}`} className="mr-4">
                      <button className="bg-blue-500 text-white font-semibold px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Ver peña
                      </button>
                    </Link>
                    <button
                      onClick={() => handleDeleteSquad(squad.id)}
                      className="bg-red-500 text-white font-semibold px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-red-500"
                    >
                      Borrar peña
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
          <div className="mt-8">
            <h2 className="text-2xl font-bold mb-4">Create a new squad</h2>
            <div className="flex">
              <input
                type="text"
                value={newSquadName}
                onChange={(e) => setNewSquadName(e.target.value)}
                className="border border-gray-300 rounded-l-lg px-4 py-2 w-full sm:w-2/3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter squad name"
              />
              <button
                onClick={handleCreateSquad}
                className="bg-blue-500 text-white font-semibold px-4 py-2 rounded-r-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                Create Squad
              </button>
            </div>
          </div>
        </div>
      )}
    </>
  );
};

export default Squad;
