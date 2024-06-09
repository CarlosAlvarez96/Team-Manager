import { get } from "../api/apiService";

export default async function divideTeams(players) {
  const playersWithStats = await Promise.all(
    players.map(async (player) => {
      const response = await get(`/individual-stats/user/${player.id}`);
      return { ...player, user_id: response.user_id, ...response };
    })
  );

  const goalkeepers = playersWithStats.filter((player) =>
    player.position.split(",").includes("PT")
  );
  const otherPlayers = playersWithStats.filter(
    (player) => !player.position.split(",").includes("PT")
  );

  if (goalkeepers.length < 2) {
    throw new Error(
      "Debe haber al menos dos porteros para dividir en dos equipos."
    );
  }

  const team1 = [];
  const team2 = [];

  team1.push(goalkeepers[0].user_id);
  team2.push(goalkeepers[1].user_id);

  function calculateAverage(player) {
    return (
      (player.pace +
        player.shooting +
        player.physical +
        player.defending +
        player.dribbling +
        player.passing) /
      6
    );
  }

  otherPlayers.sort((a, b) => calculateAverage(b) - calculateAverage(a));

  for (let i = 0; i < otherPlayers.length; i++) {
    if (i % 2 === 0) {
      team1.push(otherPlayers[i].user_id);
    } else {
      team2.push(otherPlayers[i].user_id);
    }
  }

  if (team1.length !== team2.length) {
    const lastPlayer = team1.length > team2.length ? team1.pop() : team2.pop();
    if (team1.length > team2.length) {
      team2.push(lastPlayer);
    } else {
      team1.push(lastPlayer);
    }
  }

  return {
    equipo1: team1,
    equipo2: team2,
  };
}
