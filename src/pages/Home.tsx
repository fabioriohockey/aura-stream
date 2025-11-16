import Navbar from "@/components/Navbar";
import HeroSection from "@/components/HeroSection";
import DoramaRow from "@/components/DoramaRow";
import heroDrama from "@/assets/hero-drama.jpg";
import drama1 from "@/assets/drama-1.jpg";
import drama2 from "@/assets/drama-2.jpg";
import drama3 from "@/assets/drama-3.jpg";
import drama4 from "@/assets/drama-4.jpg";
import drama5 from "@/assets/drama-5.jpg";
import drama6 from "@/assets/drama-6.jpg";

const Home = () => {
  const featuredDorama = {
    id: "1",
    title: "Corações Destinados",
    description:
      "Uma emocionante história de destino, amor e segundas chances ambientada no vibrante cenário de Seul. Quando duas almas de mundos diferentes se encontram, sua jornada se torna uma história inesquecível de romance e destino.",
    backdrop: heroDrama,
    rating: 9.2,
    year: 2024,
    genres: ["Romance", "Drama", "Fantasia"],
  };

  const trendingDoramas = [
    { id: "1", title: "Corações Destinados", poster: drama1, rating: 9.2, year: 2024, episodes: 16 },
    { id: "2", title: "Guerreiro das Sombras", poster: drama2, rating: 8.8, year: 2024, episodes: 24 },
    { id: "3", title: "Sonhos de Xangai", poster: drama3, rating: 9.0, year: 2024, episodes: 20 },
    { id: "4", title: "Detetive da Meia-Noite", poster: drama4, rating: 8.7, year: 2024, episodes: 12 },
    { id: "5", title: "Dias de Cerejeira", poster: drama5, rating: 8.9, year: 2024, episodes: 10 },
    { id: "6", title: "História de Amor em Bangkok", poster: drama6, rating: 8.6, year: 2024, episodes: 14 },
  ];

  const romanticDoramas = [
    { id: "1", title: "Corações Destinados", poster: drama1, rating: 9.2, year: 2024, episodes: 16 },
    { id: "3", title: "Sonhos de Xangai", poster: drama3, rating: 9.0, year: 2024, episodes: 20 },
    { id: "5", title: "Dias de Cerejeira", poster: drama5, rating: 8.9, year: 2024, episodes: 10 },
    { id: "6", title: "História de Amor em Bangkok", poster: drama6, rating: 8.6, year: 2024, episodes: 14 },
  ];

  const actionDoramas = [
    { id: "2", title: "Guerreiro das Sombras", poster: drama2, rating: 8.8, year: 2024, episodes: 24 },
    { id: "4", title: "Detetive da Meia-Noite", poster: drama4, rating: 8.7, year: 2024, episodes: 12 },
  ];

  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      
      {/* Spacing for fixed navbar */}
      <div className="pt-16">
        <HeroSection {...featuredDorama} />
        
        <div className="py-12 space-y-8">
          <DoramaRow title="Em Alta Agora" doramas={trendingDoramas} />
          <DoramaRow title="Dramas Românticos" doramas={romanticDoramas} />
          <DoramaRow title="Ação & Suspense" doramas={actionDoramas} />
          <DoramaRow title="Continue Assistindo" doramas={trendingDoramas} />
        </div>
      </div>
    </div>
  );
};

export default Home;
