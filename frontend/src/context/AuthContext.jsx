import { createContext, useContext, useState, useEffect } from "react";

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [token, setToken] = useState(() => sessionStorage.getItem("token") || null);
  const [email, setEmailState] = useState(() => sessionStorage.getItem("email") || null);
  const [sessionTimer, setSessionTimer] = useState(null);

  const login = (newToken, userEmail) => {
    setToken(newToken);
    sessionStorage.setItem("token", newToken);
    sessionStorage.setItem("email", userEmail);

    setEmailState(userEmail);

    // Iniciar temporizador para cerrar sesión después de una hora de inactividad
    const timer = setTimeout(logout, 3600000); // 3600000 ms = 1 hora
    setSessionTimer(timer);
  };

  const logout = () => {
    setToken(null);
    setEmailState(null); // Eliminar el email al hacer logout
    sessionStorage.removeItem("token");
    sessionStorage.removeItem("email");
    clearTimeout(sessionTimer);
  };

  useEffect(() => {
    const handleUnload = () => {
      clearTimeout(sessionTimer);
    };

    window.addEventListener("unload", handleUnload);

    // Iniciar temporizador para cerrar sesión después de una hora de inactividad
    const timer = setTimeout(logout, 3600000); // 3600000 ms = 1 hora
    setSessionTimer(timer);

    return () => {
      window.removeEventListener("unload", handleUnload);
      clearTimeout(sessionTimer);
    };
  }, []);

  const resetSessionTimer = () => {
    clearTimeout(sessionTimer);
    const timer = setTimeout(logout, 3600000); // 3600000 ms = 1 hora
    setSessionTimer(timer);
  };

  return (
    <AuthContext.Provider
      value={{
        token,
        email,
        setEmail: setEmailState,
        login,
        logout,
        resetSessionTimer,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error("useAuth must be used within an AuthProvider");
  }
  return context;
};
