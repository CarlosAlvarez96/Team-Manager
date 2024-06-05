import { Outlet } from 'react-router-dom';
import Footer from '../components/Footer';
import Header from '../components/Header';

const RootPage = () => {
  return (
      <div className={`min-h-screen flex flex-col`}>
        <Header />
        <div className="grid grid-cols-1 md:grid-cols-8 flex-1 ">
          <div className={`col-span-1 md:col-span-8 h-full`}>
            <Outlet/>
          </div>
        </div>
        <Footer />
      </div>
  );
};

export default RootPage;