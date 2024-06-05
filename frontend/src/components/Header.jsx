// Header.js
import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../context/AuthContext";

const Header = () => {
  const { logout, token } = useAuth();
  const navigate = useNavigate();
  const [menuOpen, setMenuOpen] = useState(false);
  const [hoverClass, setHoverClass] = useState("");

  const handleLogin = () => {
    navigate("/login");
    closeMenu();
  };

  const handleLogout = () => {
    logout();
    closeMenu();
  };

  const handleRegister = () => {
    navigate("/register");
    closeMenu();
  };

  const handleSquad = () => {
    navigate("/");
    closeMenu();
  };

  
  const handleMyStats = () => {
    navigate("/stats");
    closeMenu();
  };

  const handleGames = () => {
    navigate("/game");
    closeMenu();
  };


  const resetHoverClass = () => {
    setHoverClass("");
  };

  const toggleMenu = () => {
    setMenuOpen(!menuOpen);
  };

  const closeMenu = () => {
    setMenuOpen(false);
  };

  return (
    <div className={`bg-green-500`}>
      <div className="flex flex-col md:flex-row justify-between items-center p-4 ">
        <div className="flex items-center">
          <h2 className={`text-4xl font-bold p-2 m-2 `}>
            Squad Manager
          </h2>
        </div>
        <button
          className="md:hidden focus:outline-none"
          onClick={toggleMenu}
        >
          <svg
            className={`w-6 h-6 text- hover:text-${hoverClass}`}
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth="2"
              d="M4 6h16M4 12h16m-7 6h7"
            ></path>
          </svg>
        </button>
        <nav className={`md:flex md:items-center md:w-auto ${menuOpen ? 'block' : 'hidden'}`}>
          <ul className="flex flex-col md:flex-row justify-between space-y-2 md:space-y-0 md:space-x-4 mt-4 md:mt-0">
            <button
              className={`bg- font-bold px-4 py-2 rounded`}
            >
            </button>
            <button
              onClick={handleSquad}
              className={`p-2 rounded-lg hover:bg-green-700 text-white text-lg border border-transparent focus:outline-none focus:border-green-700 focus:ring-2 focus:ring-green-700`}
            >
              Equipos
            </button>

            <button
              onClick={handleMyStats}
              className={`p-2 rounded-lg hover:bg-green-700 text-white text-lg border border-transparent focus:outline-none focus:border-green-700 focus:ring-2 focus:ring-green-700`}
            >
              Estadísitcas
            </button>
            <button
              onClick={handleGames}
              className={`p-2 rounded-lg hover:bg-green-700 text-white text-lg border border-transparent focus:outline-none focus:border-green-700 focus:ring-2 focus:ring-green-700`}
            >
              Partidos
            </button>
            {!token && (
              <div>
                <button
                  onClick={handleLogin}
                  className={`p-2 rounded-lg hover:bg-green-700 text-white text-lg border border-transparent focus:outline-none focus:border-green-700 focus:ring-2 focus:ring-green-700`}
                    onMouseLeave={resetHoverClass}
                >
                  Login
                </button>
                <button
                  onClick={handleRegister}
                  className={`p-2 rounded-lg hover:bg-green-700 text-white text-lg border border-transparent focus:outline-none focus:border-green-700 focus:ring-2 focus:ring-green-700`}
                    onMouseLeave={resetHoverClass}
                >
                  Register
                </button>
              </div>
            )}
            {token && (
              <button
                onClick={handleLogout}
                className={`p-2 rounded-lg hover:bg-green-700 text-white text-lg border border-transparent focus:outline-none focus:border-green-700 focus:ring-2 focus:ring-green-700`}
                onMouseLeave={resetHoverClass}
              >
                Logout
              </button>
            )}
          </ul>
        </nav>
      </div>
    </div>
  );
};


export default Header;