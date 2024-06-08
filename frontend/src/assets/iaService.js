export default async function divideTeams(players) {
    // Separar los porteros del resto de jugadores
    const goalkeepers = players.filter(player => player.position === 'PT');
    const otherPlayers = players.filter(player => player.position !== 'PT');
  
    // Verificar que hay al menos dos porteros
    if (goalkeepers.length < 2) {
      throw new Error('Debe haber al menos dos porteros para dividir en dos equipos.');
    }
  
    // Inicializar los equipos
    const team1 = [];
    const team2 = [];
  
    // Asignar un portero a cada equipo
    team1.push(goalkeepers[0].user_id);
    team2.push(goalkeepers[1].user_id);
  
    // Función para calcular la media de estadísticas de un jugador
    function calculateAverage(player) {
      return (player.pace + player.shooting + player.physical + player.defending + player.dribbling + player.passing) / 6;
    }
  
    // Ordenar los jugadores restantes por su media de estadísticas
    otherPlayers.sort((a, b) => calculateAverage(b) - calculateAverage(a));
  
    // Distribuir los jugadores restantes equitativamente entre los dos equipos
    for (let i = 0; i < otherPlayers.length; i++) {
      if (i % 2 === 0) {
        team1.push(otherPlayers[i].user_id);
      } else {
        team2.push(otherPlayers[i].user_id);
      }
    }
  
    // Asegurarse de que ambos equipos tienen el mismo número de jugadores
    if (team1.length !== team2.length) {
      const lastPlayer = team1.length > team2.length ? team1.pop() : team2.pop();
      if (team1.length > team2.length) {
        team2.push(lastPlayer);
      } else {
        team1.push(lastPlayer);
      }
    }
  
    // Devolver los equipos como un objeto JSON
    return {
      equipo1: team1,
      equipo2: team2
    };
  }

