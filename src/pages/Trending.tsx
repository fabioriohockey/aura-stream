import Navbar from "@/components/Navbar";
import DoramaCard from "@/components/DoramaCard";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { TrendingUp, Flame, Star } from "lucide-react";
import drama1 from "@/assets/drama-1.jpg";
import drama2 from "@/assets/drama-2.jpg";
import drama3 from "@/assets/drama-3.jpg";
import drama4 from "@/assets/drama-4.jpg";
import drama5 from "@/assets/drama-5.jpg";
import drama6 from "@/assets/drama-6.jpg";

const Trending = () => {
  const trendingNow = [
    { id: "1", title: "Corações Destinados", poster: drama1, rating: 9.2, year: 2024, episodes: 16, trend: "+15%" },
    { id: "2", title: "Guerreiro das Sombras", poster: drama2, rating: 8.8, year: 2024, episodes: 24, trend: "+12%" },
    { id: "3", title: "Sonhos de Xangai", poster: drama3, rating: 9.0, year: 2024, episodes: 20, trend: "+10%" },
    { id: "4", title: "Detetive da Meia-Noite", poster: drama4, rating: 8.7, year: 2024, episodes: 12, trend: "+8%" },
    { id: "5", title: "Dias de Cerejeira", poster: drama5, rating: 8.9, year: 2024, episodes: 10, trend: "+6%" },
    { id: "6", title: "História de Amor em Bangkok", poster: drama6, rating: 8.6, year: 2024, episodes: 14, trend: "+5%" },
  ];

  const topRated = [
    { id: "1", title: "Corações Destinados", poster: drama1, rating: 9.2, year: 2024, episodes: 16 },
    { id: "3", title: "Sonhos de Xangai", poster: drama3, rating: 9.0, year: 2024, episodes: 20 },
    { id: "5", title: "Dias de Cerejeira", poster: drama5, rating: 8.9, year: 2024, episodes: 10 },
  ];

  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      
      <div className="pt-16">
        <div className="container mx-auto px-6 py-12">
          {/* Header */}
          <div className="mb-8">
            <div className="flex items-center space-x-3 mb-2">
              <Flame className="h-8 w-8 text-primary" />
              <h1 className="text-4xl font-bold text-foreground">Em Alta</h1>
            </div>
            <p className="text-muted-foreground">Os doramas mais populares do momento</p>
          </div>

          {/* Trending Now Section */}
          <section className="mb-12">
            <div className="flex items-center space-x-2 mb-6">
              <TrendingUp className="h-5 w-5 text-primary" />
              <h2 className="text-2xl font-bold text-foreground">Trending Agora</h2>
            </div>

            <div className="space-y-4">
              {trendingNow.map((dorama, index) => (
                <Card key={dorama.id} className="overflow-hidden border-border hover:border-primary transition-colors cursor-pointer">
                  <CardContent className="p-0">
                    <div className="flex items-center">
                      {/* Rank */}
                      <div className="w-16 h-full bg-primary/10 flex items-center justify-center">
                        <span className="text-3xl font-bold text-primary">#{index + 1}</span>
                      </div>

                      {/* Poster */}
                      <img
                        src={dorama.poster}
                        alt={dorama.title}
                        className="w-24 h-36 object-cover"
                      />

                      {/* Info */}
                      <div className="flex-1 p-4 flex items-center justify-between">
                        <div className="space-y-2">
                          <h3 className="text-xl font-semibold text-foreground">{dorama.title}</h3>
                          <div className="flex items-center space-x-4 text-sm text-muted-foreground">
                            <div className="flex items-center space-x-1">
                              <Star className="h-4 w-4 fill-primary text-primary" />
                              <span>{dorama.rating}</span>
                            </div>
                            <span>{dorama.year}</span>
                            <span>{dorama.episodes} eps</span>
                          </div>
                        </div>

                        <Badge className="bg-primary text-primary-foreground">
                          {dorama.trend}
                        </Badge>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>
          </section>

          {/* Top Rated Section */}
          <section>
            <div className="flex items-center space-x-2 mb-6">
              <Star className="h-5 w-5 text-primary fill-primary" />
              <h2 className="text-2xl font-bold text-foreground">Melhor Avaliados</h2>
            </div>

            <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
              {topRated.map((dorama) => (
                <DoramaCard key={dorama.id} {...dorama} />
              ))}
            </div>
          </section>
        </div>
      </div>
    </div>
  );
};

export default Trending;
