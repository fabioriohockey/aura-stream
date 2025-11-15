import { useParams, useSearchParams, Link } from "react-router-dom";
import Navbar from "@/components/Navbar";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { ChevronLeft, ChevronRight, ThumbsUp, MessageCircle, Share2 } from "lucide-react";
import drama1 from "@/assets/drama-1.jpg";

const Player = () => {
  const { id } = useParams();
  const [searchParams] = useSearchParams();
  const currentEpisode = parseInt(searchParams.get("episode") || "1");

  const dorama = {
    id: "1",
    title: "Destined Hearts",
    poster: drama1,
    totalEpisodes: 16,
    episodes: [
      { number: 1, title: "Fated Encounter", duration: "65 min" },
      { number: 2, title: "Crossing Paths", duration: "60 min" },
      { number: 3, title: "Hidden Feelings", duration: "62 min" },
      { number: 4, title: "Confession", duration: "58 min" },
      { number: 5, title: "Distance", duration: "63 min" },
    ],
  };

  const comments = [
    {
      user: "DramaLover123",
      text: "This episode was absolutely amazing! The chemistry between the leads is incredible.",
      likes: 124,
      time: "2 hours ago",
    },
    {
      user: "KDramaFan",
      text: "I can't wait for the next episode! This show keeps getting better and better.",
      likes: 89,
      time: "5 hours ago",
    },
  ];

  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      
      <div className="pt-16">
        <div className="container mx-auto px-6 py-8">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* Main Player Area */}
            <div className="lg:col-span-2 space-y-6">
              {/* Video Player */}
              <Card className="overflow-hidden border-none shadow-lg bg-black">
                <div className="aspect-video bg-black flex items-center justify-center">
                  <p className="text-white text-lg">Video Player Placeholder</p>
                </div>
              </Card>

              {/* Episode Info & Controls */}
              <div className="space-y-4">
                <div className="flex items-center justify-between">
                  <div>
                    <h1 className="text-2xl md:text-3xl font-bold text-foreground">
                      {dorama.title}
                    </h1>
                    <p className="text-muted-foreground">
                      Episode {currentEpisode}: {dorama.episodes.find(e => e.number === currentEpisode)?.title}
                    </p>
                  </div>
                  <Button variant="outline" className="border-foreground/20">
                    <Share2 className="h-4 w-4 mr-2" />
                    Share
                  </Button>
                </div>

                {/* Navigation */}
                <div className="flex items-center space-x-4">
                  <Link to={`/watch/${id}?episode=${Math.max(1, currentEpisode - 1)}`}>
                    <Button
                      variant="outline"
                      disabled={currentEpisode === 1}
                      className="border-foreground/20"
                    >
                      <ChevronLeft className="h-4 w-4 mr-2" />
                      Previous
                    </Button>
                  </Link>
                  <Link to={`/watch/${id}?episode=${Math.min(dorama.totalEpisodes, currentEpisode + 1)}`}>
                    <Button
                      variant="outline"
                      disabled={currentEpisode === dorama.totalEpisodes}
                      className="border-foreground/20"
                    >
                      Next
                      <ChevronRight className="h-4 w-4 ml-2" />
                    </Button>
                  </Link>
                  <Link to={`/dorama/${id}`}>
                    <Button variant="ghost" className="hover:text-primary">
                      View All Episodes
                    </Button>
                  </Link>
                </div>
              </div>

              {/* Comments Section */}
              <div className="space-y-6">
                <div className="flex items-center space-x-2">
                  <MessageCircle className="h-5 w-5 text-primary" />
                  <h2 className="text-xl font-bold text-foreground">Comments</h2>
                </div>

                {/* Add Comment */}
                <Card className="border-border">
                  <CardContent className="p-4 space-y-3">
                    <Textarea
                      placeholder="Share your thoughts about this episode..."
                      className="min-h-[100px] resize-none border-border focus:border-primary"
                    />
                    <div className="flex justify-end">
                      <Button className="bg-primary hover:bg-primary-light text-primary-foreground">
                        Post Comment
                      </Button>
                    </div>
                  </CardContent>
                </Card>

                {/* Comment List */}
                <div className="space-y-4">
                  {comments.map((comment, index) => (
                    <Card key={index} className="border-border">
                      <CardContent className="p-4 space-y-3">
                        <div className="flex items-center justify-between">
                          <p className="font-semibold text-foreground">{comment.user}</p>
                          <p className="text-sm text-muted-foreground">{comment.time}</p>
                        </div>
                        <p className="text-foreground/80">{comment.text}</p>
                        <Button variant="ghost" size="sm" className="hover:text-primary">
                          <ThumbsUp className="h-4 w-4 mr-2" />
                          {comment.likes}
                        </Button>
                      </CardContent>
                    </Card>
                  ))}
                </div>
              </div>
            </div>

            {/* Sidebar - Episode List */}
            <div className="lg:col-span-1">
              <Card className="border-border sticky top-24">
                <CardContent className="p-6 space-y-4">
                  <h3 className="text-lg font-bold text-foreground">Episodes</h3>
                  <div className="space-y-2 max-h-[600px] overflow-y-auto">
                    {dorama.episodes.map((episode) => (
                      <Link
                        key={episode.number}
                        to={`/watch/${id}?episode=${episode.number}`}
                      >
                        <Card
                          className={`cursor-pointer transition-colors ${
                            episode.number === currentEpisode
                              ? "bg-primary/10 border-primary"
                              : "hover:bg-accent border-border"
                          }`}
                        >
                          <CardContent className="p-3">
                            <div className="flex items-start space-x-3">
                              <div
                                className={`font-semibold rounded px-2 py-1 text-sm ${
                                  episode.number === currentEpisode
                                    ? "bg-primary text-primary-foreground"
                                    : "bg-muted text-muted-foreground"
                                }`}
                              >
                                {episode.number}
                              </div>
                              <div className="flex-1 min-w-0">
                                <p
                                  className={`font-medium text-sm truncate ${
                                    episode.number === currentEpisode
                                      ? "text-primary"
                                      : "text-foreground"
                                  }`}
                                >
                                  {episode.title}
                                </p>
                                <p className="text-xs text-muted-foreground">{episode.duration}</p>
                              </div>
                            </div>
                          </CardContent>
                        </Card>
                      </Link>
                    ))}
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Player;
