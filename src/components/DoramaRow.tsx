import { ChevronLeft, ChevronRight } from "lucide-react";
import DoramaCard from "./DoramaCard";
import { Button } from "@/components/ui/button";
import { useRef } from "react";

interface Dorama {
  id: string;
  title: string;
  poster: string;
  rating: number;
  year: number;
  episodes?: number;
}

interface DoramaRowProps {
  title: string;
  doramas: Dorama[];
}

const DoramaRow = ({ title, doramas }: DoramaRowProps) => {
  const scrollRef = useRef<HTMLDivElement>(null);

  const scroll = (direction: "left" | "right") => {
    if (scrollRef.current) {
      const scrollAmount = direction === "left" ? -800 : 800;
      scrollRef.current.scrollBy({ left: scrollAmount, behavior: "smooth" });
    }
  };

  return (
    <section className="mb-12">
      <div className="container mx-auto px-6">
        <h2 className="text-2xl font-bold text-foreground mb-6">{title}</h2>
        
        <div className="relative group">
          {/* Navigation Buttons */}
          <Button
            variant="ghost"
            size="icon"
            className="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-background/80 hover:bg-background/95 opacity-0 group-hover:opacity-100 transition-opacity shadow-lg h-12 w-12 rounded-full"
            onClick={() => scroll("left")}
          >
            <ChevronLeft className="h-6 w-6" />
          </Button>
          
          <Button
            variant="ghost"
            size="icon"
            className="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-background/80 hover:bg-background/95 opacity-0 group-hover:opacity-100 transition-opacity shadow-lg h-12 w-12 rounded-full"
            onClick={() => scroll("right")}
          >
            <ChevronRight className="h-6 w-6" />
          </Button>

          {/* Cards Container */}
          <div
            ref={scrollRef}
            className="flex space-x-4 overflow-x-auto scrollbar-hide scroll-smooth"
            style={{ scrollbarWidth: "none", msOverflowStyle: "none" }}
          >
            {doramas.map((dorama) => (
              <div key={dorama.id} className="flex-shrink-0 w-48">
                <DoramaCard {...dorama} />
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};

export default DoramaRow;
