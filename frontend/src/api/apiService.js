const apiUrl = 'http://localhost';

const replacer = (key, value) => {
  if (typeof value === "number") {
    return value;
  }
  return value;
};

const get = async (endpoint) => {
  const token = sessionStorage.getItem("token");
  const response = await fetch(apiUrl + endpoint, {
    headers: {
      Authorization: `Bearer ${token}`,
      accept: "application/json",
    },
  });
  if (!response.ok) {
    console.error(`Error en la petición GET al endpoint: ${endpoint} (${response.status} ${response.statusText})`);
    const errorData = await response.json(); // Obtener detalles del error si están disponibles
    console.error(errorData);
    throw new Error(`Error en la petición GET al endpoint: ${endpoint} (${response.status} ${response.statusText})`);
  }
  const data = await response.json();
  console.log(data);
  return data;
};


const post = async (endpoint, dto) => {
  const token = sessionStorage.getItem("token");
  const response = await fetch(`${apiUrl}${endpoint}`, {
    method: "POST",
    headers: {
      Authorization: `Bearer ${token}`,
      accept: "application/json",
      "Content-Type": "application/json",
    },
    body: JSON.stringify(dto, replacer),
   });
   console.log(JSON.stringify(dto, replacer));
  //  console.log(JSON.stringify(dto, replacer));
  if (!response.ok) {
    throw new Error(
      `Error en la petición POST al endpoint: ${endpoint} (${response.status} ${response.statusText})`
    );
  }
  const data = await response.json();
  return data;
};

const put = async (endpoint, dto) => {
  const token = sessionStorage.getItem("token");
  const response = await fetch(`${apiUrl}${endpoint}`, {
    method: "PUT",
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
    body: JSON.stringify(dto),
  });
  if (!response.ok) {
    throw new Error(
      `Error en la petición PUT al endpoint: ${endpoint} (${response.status} ${response.statusText})`
    );
  }
  const data = await response.json();
  return data;
};


const deleteMethod = async (endpoint) => {
  const token = sessionStorage.getItem("token");
  await fetch(`${apiUrl}${endpoint}`, {
    method: "DELETE",
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  });
};

export { get, post, put, deleteMethod };