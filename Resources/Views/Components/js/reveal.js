class TextReveal {
    constructor(element, options) {
      this.defaultOptions = {
        duration: 1000, // Tempo de duração da transição em milissegundos
        delay: 2000,   // Atraso antes do início da transição em milissegundos
        loop: true     // Indica se o efeito text-reveal deve ser em loop
      };
  
      this.element = element;
      this.options = Object.assign({}, this.defaultOptions, options);
      this.isRevealing = false;
      this.currentIndex = 0;
  
      this.init();
    }
  
    init() {
      this.handleScroll();
    }
  
    handleScroll() {
      if (!this.isRevealing) {
        this.isRevealing = true;
        this.revealElement();
      }
    }
  
    revealElement() {
      const text = this.options.texts[this.currentIndex];
      const characters = text.split('');
      const revealDuration = this.options.duration / characters.length;
  
      let currentIndex = 0;
  
      const applyTextReveal = () => {
        const revealedText = characters.slice(0, currentIndex + 1).join('');
        this.element.innerText = revealedText;
  
        currentIndex++;
  
        if (currentIndex < characters.length) {
          setTimeout(applyTextReveal, revealDuration);
        } else {
          setTimeout(() => {
            this.hideElement();
          }, this.options.delay);
        }
      };
  
      applyTextReveal();
    }
  
    hideElement() {
      const text = this.element.innerText;
      const characters = text.split('');
      const hideDuration = this.options.duration / characters.length;
  
      let currentIndex = characters.length - 1;
  
      const applyTextHide = () => {
        const hiddenText = characters.slice(0, currentIndex).join('');
        this.element.innerText = hiddenText;
  
        currentIndex--;
  
        if (currentIndex >= 0) {
          setTimeout(applyTextHide, hideDuration);
        } else {
          this.currentIndex = (this.currentIndex + 1) % this.options.texts.length;
          this.isRevealing = false;
  
          if (this.options.loop || this.currentIndex !== 0) {
            setTimeout(() => {
              this.handleScroll();
            }, this.options.delay);
          }
        }
      };
  
      applyTextHide();
    }
  }
  
  // Exemplo de uso com o efeito text-reveal
  
  const element = document.querySelector('.reveal-text');
  const options = {
    texts: ['Dev Fullstack', 'Desing Grafico','Social Midia'], // Defina os textos desejados
    duration: 2000, // Tempo de duração da transição em milissegundos
    delay: 1000,   // Atraso antes do início da transição em milissegundos
    loop: true    // Indica se o efeito text-reveal deve ser em loop
  };
  
  const textReveal = new TextReveal(element, options);
  
  
  
  