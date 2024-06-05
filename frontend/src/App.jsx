import { RouterProvider, createBrowserRouter } from "react-router-dom";
import RootPage from "./pages/RootPage";
import ProtectedRoute from "./utils/ProtectedRoute";
import Squad from "./pages/Squad";
import Game from "./components/Game";
import SquadDetail from "./pages/SquadDetail";
import ErrorPage from "./pages/ErrorPage";
import Login from "./pages/Login";
import Stats from "./pages/Stats";
import Register from "./pages/Register";
import { AuthProvider } from "./context/AuthContext";


function App() {
  const router = createBrowserRouter([
    {
      path: "/",
      element: <RootPage />,
      errorElement: <ErrorPage />,
      children: [
        {
          element: <ProtectedRoute redirectPath="/login" />,
          children: [
            { index: true, element: <Squad /> },
            { path: "squaddetail/:id", element: <SquadDetail /> },
            { path: "stats", element: <Stats /> },
            { path: "game", element: <Game /> }
          ],
        },
        {
          path: "login",
          element: <Login />,
        },
        {
          path: "register",
          element: <Register />,
        },
      ],
    },
  ]);
  return (
    <AuthProvider>
        <RouterProvider router={router} />
    </AuthProvider>
  );
}

export default App;