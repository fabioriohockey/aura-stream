import Navbar from "@/components/Navbar";
import DoramaCard from "@/components/DoramaCard";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Card, CardContent } from "@/components/ui/card";
import { Progress } from "@/components/ui/progress";
import { Heart, Clock, CheckCircle2 } from "lucide-react";
import drama1 from "@/assets/drama-1.jpg";
import drama2 from "@/assets/drama-2.jpg";
import drama3 from "@/assets/drama-3.jpg";
import drama4 from "@/assets/drama-4.jpg";

const MyList = () => {
  const favorites = [
    { id: "1", title: "Corações Destinados", poster: drama1, rating: 9.2, year: 2024, episodes: 16 },
    { id: "3", title: "Sonhos de Xangai", poster: drama3, rating: 9.0, year: 2024, episodes: 20 },
  ];

  const watching = [
    {
      id: "1",
      title: "Corações Destinados",
      poster: drama1,
      currentEpisode: 8,
      totalEpisodes: 16,
      progress: 50,
    },
    {
      id: "4",
      title: "Detetive da Meia-Noite",
      poster: drama4,
      currentEpisode: 3,
      totalEpisodes: 12,
      progress: 25,
    },
  ];

  const completed = [
    { id: "2", title: "Guerreiro das Sombras", poster: drama2, rating: 8.8, year: 2024, episodes: 24 },
  ];

  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      
      <div className="pt-16">
        <div className="container mx-auto px-6 py-12">
          {/* Header */}
          <div className="mb-8">
            <h1 className="text-4xl font-bold text-foreground mb-2">Minha Lista</h1>
            <p className="text-muted-foreground">Gerencie seus doramas favoritos</p>
          </div>

          {/* Tabs */}
          <Tabs defaultValue="watching" className="space-y-8">
            <TabsList className="bg-secondary">
              <TabsTrigger value="watching" className="data-[state=active]:bg-primary data-[state=active]:text-primary-foreground">
                <Clock className="h-4 w-4 mr-2" />
                Assistindo
              </TabsTrigger>
              <TabsTrigger value="favorites" className="data-[state=active]:bg-primary data-[state=active]:text-primary-foreground">
                <Heart className="h-4 w-4 mr-2" />
                Favoritos
              </TabsTrigger>
              <TabsTrigger value="completed" className="data-[state=active]:bg-primary data-[state=active]:text-primary-foreground">
                <CheckCircle2 className="h-4 w-4 mr-2" />
                Completos
              </TabsTrigger>
            </TabsList>

            {/* Continue Watching */}
            <TabsContent value="watching" className="space-y-6">
              <div>
                <h2 className="text-2xl font-bold text-foreground mb-4">Continue Assistindo</h2>
                <div className="space-y-4">
                  {watching.map((dorama) => (
                    <Card key={dorama.id} className="overflow-hidden border-border hover:border-primary transition-colors cursor-pointer">
                      <CardContent className="p-0">
                        <div className="flex items-center">
                          <img
                            src={dorama.poster}
                            alt={dorama.title}
                            className="w-32 h-48 object-cover"
                          />
                          <div className="flex-1 p-6 space-y-3">
                            <h3 className="text-xl font-semibold text-foreground">{dorama.title}</h3>
                            <div className="space-y-2">
                              <div className="flex items-center justify-between text-sm text-muted-foreground">
                                <span>Episódio {dorama.currentEpisode} de {dorama.totalEpisodes}</span>
                                <span>{dorama.progress}%</span>
                              </div>
                              <Progress value={dorama.progress} className="h-2" />
                            </div>
                          </div>
                        </div>
                      </CardContent>
                    </Card>
                  ))}
                </div>
              </div>
            </TabsContent>

            {/* Favorites */}
            <TabsContent value="favorites" className="space-y-6">
              <div>
                <h2 className="text-2xl font-bold text-foreground mb-4">Meus Favoritos</h2>
                {favorites.length > 0 ? (
                  <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                    {favorites.map((dorama) => (
                      <DoramaCard key={dorama.id} {...dorama} />
                    ))}
                  </div>
                ) : (
                  <Card className="border-border">
                    <CardContent className="p-12 text-center">
                      <Heart className="h-12 w-12 text-muted-foreground mx-auto mb-4" />
                      <p className="text-muted-foreground">Você ainda não adicionou nenhum dorama aos favoritos</p>
                    </CardContent>
                  </Card>
                )}
              </div>
            </TabsContent>

            {/* Completed */}
            <TabsContent value="completed" className="space-y-6">
              <div>
                <h2 className="text-2xl font-bold text-foreground mb-4">Doramas Completos</h2>
                {completed.length > 0 ? (
                  <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                    {completed.map((dorama) => (
                      <DoramaCard key={dorama.id} {...dorama} />
                    ))}
                  </div>
                ) : (
                  <Card className="border-border">
                    <CardContent className="p-12 text-center">
                      <CheckCircle2 className="h-12 w-12 text-muted-foreground mx-auto mb-4" />
                      <p className="text-muted-foreground">Você ainda não completou nenhum dorama</p>
                    </CardContent>
                  </Card>
                )}
              </div>
            </TabsContent>
          </Tabs>
        </div>
      </div>
    </div>
  );
};

export default MyList;
