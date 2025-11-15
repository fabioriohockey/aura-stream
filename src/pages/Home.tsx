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
    title: "Destined Hearts",
    description:
      "A heartwarming tale of fate, love, and second chances set against the vibrant backdrop of Seoul. When two souls from different worlds collide, their journey becomes an unforgettable story of romance and destiny.",
    backdrop: heroDrama,
    rating: 9.2,
    year: 2024,
    genres: ["Romance", "Drama", "Fantasy"],
  };

  const trendingDoramas = [
    { id: "1", title: "Destined Hearts", poster: drama1, rating: 9.2, year: 2024, episodes: 16 },
    { id: "2", title: "Shadow Warrior", poster: drama2, rating: 8.8, year: 2024, episodes: 24 },
    { id: "3", title: "Shanghai Dreams", poster: drama3, rating: 9.0, year: 2024, episodes: 20 },
    { id: "4", title: "Midnight Detective", poster: drama4, rating: 8.7, year: 2024, episodes: 12 },
    { id: "5", title: "Cherry Blossom Days", poster: drama5, rating: 8.9, year: 2024, episodes: 10 },
    { id: "6", title: "Bangkok Love Story", poster: drama6, rating: 8.6, year: 2024, episodes: 14 },
  ];

  const romanticDoramas = [
    { id: "1", title: "Destined Hearts", poster: drama1, rating: 9.2, year: 2024, episodes: 16 },
    { id: "3", title: "Shanghai Dreams", poster: drama3, rating: 9.0, year: 2024, episodes: 20 },
    { id: "5", title: "Cherry Blossom Days", poster: drama5, rating: 8.9, year: 2024, episodes: 10 },
    { id: "6", title: "Bangkok Love Story", poster: drama6, rating: 8.6, year: 2024, episodes: 14 },
  ];

  const actionDoramas = [
    { id: "2", title: "Shadow Warrior", poster: drama2, rating: 8.8, year: 2024, episodes: 24 },
    { id: "4", title: "Midnight Detective", poster: drama4, rating: 8.7, year: 2024, episodes: 12 },
  ];

  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      
      {/* Spacing for fixed navbar */}
      <div className="pt-16">
        <HeroSection {...featuredDorama} />
        
        <div className="py-12 space-y-8">
          <DoramaRow title="Trending Now" doramas={trendingDoramas} />
          <DoramaRow title="Romantic Dramas" doramas={romanticDoramas} />
          <DoramaRow title="Action & Thriller" doramas={actionDoramas} />
          <DoramaRow title="Continue Watching" doramas={trendingDoramas} />
        </div>
      </div>
    </div>
  );
};

export default Home;
