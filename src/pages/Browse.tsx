import { useState } from "react";
import Navbar from "@/components/Navbar";
import DoramaCard from "@/components/DoramaCard";
import { Button } from "@/components/ui/button";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Badge } from "@/components/ui/badge";
import { SlidersHorizontal } from "lucide-react";
import drama1 from "@/assets/drama-1.jpg";
import drama2 from "@/assets/drama-2.jpg";
import drama3 from "@/assets/drama-3.jpg";
import drama4 from "@/assets/drama-4.jpg";
import drama5 from "@/assets/drama-5.jpg";
import drama6 from "@/assets/drama-6.jpg";

const Browse = () => {
  const [selectedGenre, setSelectedGenre] = useState("all");
  const [selectedCountry, setSelectedCountry] = useState("all");
  const [selectedYear, setSelectedYear] = useState("all");

  const genres = ["Todos", "Romance", "Ação", "Comédia", "Drama", "Fantasia", "Suspense", "Histórico"];
  const countries = ["Todos", "Coreia", "Japão", "China", "Tailândia", "Taiwan"];

  const allDoramas = [
    { id: "1", title: "Corações Destinados", poster: drama1, rating: 9.2, year: 2024, episodes: 16, genre: "Romance", country: "Coreia" },
    { id: "2", title: "Guerreiro das Sombras", poster: drama2, rating: 8.8, year: 2024, episodes: 24, genre: "Ação", country: "Japão" },
    { id: "3", title: "Sonhos de Xangai", poster: drama3, rating: 9.0, year: 2024, episodes: 20, genre: "Romance", country: "China" },
    { id: "4", title: "Detetive da Meia-Noite", poster: drama4, rating: 8.7, year: 2024, episodes: 12, genre: "Suspense", country: "Coreia" },
    { id: "5", title: "Dias de Cerejeira", poster: drama5, rating: 8.9, year: 2024, episodes: 10, genre: "Romance", country: "Japão" },
    { id: "6", title: "História de Amor em Bangkok", poster: drama6, rating: 8.6, year: 2024, episodes: 14, genre: "Comédia", country: "Tailândia" },
  ];

  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      
      <div className="pt-16">
        <div className="container mx-auto px-6 py-12">
          {/* Header */}
          <div className="mb-8">
            <h1 className="text-4xl font-bold text-foreground mb-2">Explorar Doramas</h1>
            <p className="text-muted-foreground">Descubra sua próxima série favorita</p>
          </div>

          {/* Filters */}
          <div className="mb-8 space-y-6">
            {/* Genre Tags */}
            <div className="space-y-3">
              <div className="flex items-center space-x-2">
                <SlidersHorizontal className="h-4 w-4 text-primary" />
                <h3 className="font-semibold text-foreground">Gêneros</h3>
              </div>
              <div className="flex flex-wrap gap-2">
                {genres.map((genre) => (
                  <Badge
                    key={genre}
                    variant={selectedGenre === genre.toLowerCase() ? "default" : "outline"}
                    className={`cursor-pointer transition-colors ${
                      selectedGenre === genre.toLowerCase()
                        ? "bg-primary text-primary-foreground hover:bg-primary-light"
                        : "hover:border-primary hover:text-primary"
                    }`}
                    onClick={() => setSelectedGenre(genre.toLowerCase())}
                  >
                    {genre}
                  </Badge>
                ))}
              </div>
            </div>

            {/* Filters Row */}
            <div className="flex flex-wrap gap-4">
              <Select value={selectedCountry} onValueChange={setSelectedCountry}>
                <SelectTrigger className="w-48 border-border focus:border-primary">
                  <SelectValue placeholder="País" />
                </SelectTrigger>
                <SelectContent>
                  {countries.map((country) => (
                    <SelectItem key={country} value={country.toLowerCase()}>
                      {country}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>

              <Select value={selectedYear} onValueChange={setSelectedYear}>
                <SelectTrigger className="w-48 border-border focus:border-primary">
                  <SelectValue placeholder="Ano" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">Todos os Anos</SelectItem>
                  <SelectItem value="2024">2024</SelectItem>
                  <SelectItem value="2023">2023</SelectItem>
                  <SelectItem value="2022">2022</SelectItem>
                </SelectContent>
              </Select>

              <Select defaultValue="rating">
                <SelectTrigger className="w-48 border-border focus:border-primary">
                  <SelectValue placeholder="Ordenar por" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="rating">Maior Avaliação</SelectItem>
                  <SelectItem value="newest">Mais Recentes</SelectItem>
                  <SelectItem value="popular">Mais Populares</SelectItem>
                  <SelectItem value="title">Nome (A-Z)</SelectItem>
                </SelectContent>
              </Select>

              <Button variant="outline" className="border-border hover:border-primary hover:text-primary">
                Limpar Filtros
              </Button>
            </div>
          </div>

          {/* Results */}
          <div className="mb-6">
            <p className="text-muted-foreground">
              Exibindo <span className="font-semibold text-foreground">{allDoramas.length}</span> doramas
            </p>
          </div>

          {/* Grid */}
          <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            {allDoramas.map((dorama) => (
              <DoramaCard key={dorama.id} {...dorama} />
            ))}
          </div>

          {/* Load More */}
          <div className="mt-12 flex justify-center">
            <Button size="lg" variant="outline" className="border-border hover:border-primary hover:text-primary">
              Carregar Mais
            </Button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Browse;
