import '../fade.css';

const Footer = () => {
  return (
    <div className="header-gradient">
      <footer className={`flex flex-col md:flex-row justify-around p-4 `}>
        <h2 className="text-xl font-bold p-2 m-2 md:mb-0 md:mr-4">
          Proyecto Integrado C.F.G.S. Desarrollo de Aplicaciones Web
        </h2>
        <h2 className="text-xl font-bold p-2 m-2">Carlos Álvarez Martín - Hermenegildo Lanz</h2>
        <div className="flex flex-row space-x-8">
          <a href="https://github.com/CarlosAlvarez96/Team-Manager" className="text-white hover:text-blue-400">Repositorio de github</a>
          <a href="https://www.ieshlanz.es/" className="text-white hover:text-blue-400">Web del instituto</a>
        </div>
      </footer>
    </div>
  );
};

export default Footer;
