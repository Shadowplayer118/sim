const locations = [
    { 
        name: 'Bar', 
        mood: 'Social Hub', 
        difficulty: 3, 
        background: 'linear-gradient(135deg, #FF6B6B, #4ECDC4)',
        icon: '🍸',
        description: 'A vibrant social setting full of energy and possibilities'
    },
    { 
        name: 'Park', 
        mood: 'Serene Escape', 
        difficulty: 1, 
        background: 'linear-gradient(135deg, #2ecc71, #3498db)',
        icon: '🌳',
        description: 'A peaceful natural environment for relaxation and reflection'
    },
    { 
        name: 'Hotel', 
        mood: 'Luxurious Retreat', 
        difficulty: 2, 
        background: 'linear-gradient(135deg, #f1c40f, #e67e22)',
        icon: '🏨',
        description: 'A comfortable space for rest and sophisticated interactions'
    },
    { 
        name: 'Office', 
        mood: 'Professional Zone', 
        difficulty: 4, 
        background: 'linear-gradient(135deg, #34495e, #2c3e50)',
        icon: '💼',
        description: 'A challenging environment requiring focus and strategy'
    },
    { 
        name: 'Forest', 
        mood: 'Mystical Wilderness', 
        difficulty: 5, 
        background: 'linear-gradient(135deg, #006400, #008000)',
        icon: '🌲',
        description: 'An intricate and demanding natural landscape'
    }
];

const actions = [
    { 
        type: 'working', 
        energy: 70, 
        complexity: 'high',
        description: 'Focused and productive endeavors',
        icon: '💻',
        skills: ['intelligence', 'charisma']
    },
    { 
        type: 'fighting', 
        energy: 90, 
        complexity: 'extreme',
        description: 'Intense physical and mental challenges',
        icon: '🥊',
        skills: ['strength', 'intelligence']
    },
    { 
        type: 'chilling', 
        energy: 30, 
        complexity: 'low',
        description: 'Relaxed and mindful moments',
        icon: '😎',
        skills: ['charisma']
    },
    { 
        type: 'exploring', 
        energy: 60, 
        complexity: 'medium',
        description: 'Adventurous and curious exploration',
        icon: '🧭',
        skills: ['intelligence', 'strength']
    },
    { 
        type: 'networking', 
        energy: 50, 
        complexity: 'social',
        description: 'Building meaningful connections',
        icon: '🤝',
        skills: ['charisma', 'intelligence']
    }
];

class CharacterModel {
    constructor(name, color) {
        this.name = name;
        this.color = color;
        this.stats = {
            charisma: this.randomStat(),
            strength: this.randomStat(),
            intelligence: this.randomStat(),
            energy: 100,
            level: 1,
            experience: 0
        };
        this.personality = this.generatePersonality();
        this.location = this.getRandomLocation();
        this.action = this.getRandomAction();
        this.history = [];
        this.achievements = [];
    }

    randomStat() {
        return Math.floor(Math.random() * 10) + 1;
    }

    generatePersonality() {
        const personalityTraits = [
            'Adventurous', 'Cautious', 'Creative', 'Strategic', 
            'Spontaneous', 'Analytical', 'Empathetic', 'Ambitious'
        ];
        return personalityTraits[Math.floor(Math.random() * personalityTraits.length)];
    }

    getRandomLocation() {
        return locations[Math.floor(Math.random() * locations.length)].name;
    }

    getRandomAction() {
        return actions[Math.floor(Math.random() * actions.length)].type;
    }

    update() {
        this.regenerateEnergy();
        this.chooseLocation();
        this.chooseAction();
        this.gainExperience();
        this.updateHistory();
        this.checkForAchievements();
    }

    regenerateEnergy() {
        const currentAction = actions.find(a => a.type === this.action);
        const recoveryRate = currentAction.type === 'chilling' ? 15 : 5;
        this.stats.energy = Math.min(100, this.stats.energy + recoveryRate);
    }

    chooseLocation() {
        const skillBalance = (this.stats.intelligence + this.stats.charisma + this.stats.strength) / 3;
        const locationOptions = locations.filter(loc => 
            Math.abs(loc.difficulty - skillBalance) <= 2
        );

        this.location = locationOptions[Math.floor(Math.random() * locationOptions.length)].name;
    }

    chooseAction() {
        const viableActions = actions.filter(action => {
            const skillMatch = action.skills.some(skill => 
                this.stats[skill] > 5 && this.stats.energy >= action.energy / 2
            );
            return skillMatch;
        });

        const currentAction = actions.find(a => a.type === this.action);
        const energyCost = currentAction.energy;
        this.stats.energy = Math.max(0, this.stats.energy - energyCost / 10);

        this.action = viableActions[Math.floor(Math.random() * viableActions.length)].type;
    }

    gainExperience() {
        const currentAction = actions.find(a => a.type === this.action);
        const experienceMultiplier = 1 + (this.personality === 'Ambitious' ? 0.2 : 0);
        const experienceGain = Math.floor(currentAction.energy / 10 * experienceMultiplier);
        
        this.stats.experience += experienceGain;
        
        if (this.stats.experience >= 100 * this.stats.level) {
            this.levelUp();
        }
    }

    updateHistory() {
        const locationDetails = locations.find(l => l.name === this.location);
        this.history.push({
            location: this.location,
            action: this.action,
            timestamp: new Date(),
            mood: locationDetails.mood
        });

        if (this.history.length > 10) {
            this.history.shift();
        }
    }

    levelUp() {
        this.stats.level++;
        this.stats.experience = 0;
        
        const statsToBoost = ['charisma', 'strength', 'intelligence'];
        const primaryStat = statsToBoost[Math.floor(Math.random() * statsToBoost.length)];
        const secondaryStat = statsToBoost.filter(s => s !== primaryStat)[Math.floor(Math.random() * 2)];
        
        this.stats[primaryStat] += 3;
        this.stats[secondaryStat] += 1;
        
        this.achievements.push(`${this.personality} ${this.name} Leveled up to Level ${this.stats.level}!`);
    }

    checkForAchievements() {
        const currentAction = actions.find(a => a.type === this.action);
        const currentLocation = locations.find(l => l.name === this.location);

        const achievementProbability = this.personality === 'Adventurous' ? 0.2 : 0.1;
        
        if (Math.random() < achievementProbability) {
            const contextualAchievements = [
                `${this.personality} Master of ${currentLocation.name}`,
                `${currentAction.type.charAt(0).toUpperCase() + currentAction.type.slice(1)} Virtuoso`,
                `${this.personality} Energy Strategist`,
                'Versatile Location Explorer'
            ];
            this.achievements.push(contextualAchievements[Math.floor(Math.random() * contextualAchievements.length)]);
        }
    }

    getStatusReport() {
        const currentAction = actions.find(a => a.type === this.action);
        const currentLocation = locations.find(l => l.name === this.location);

        return {
            basic: `${this.name} (${this.personality}) ${currentAction.icon} ${currentAction.type} at ${currentLocation.name}`,
            personality: this.personality,
            location: {
                name: currentLocation.name,
                mood: currentLocation.mood,
                description: currentLocation.description
            },
            stats: [
                { name: 'Charisma', value: this.stats.charisma, icon: '✨' },
                { name: 'Strength', value: this.stats.strength, icon: '💪' },
                { name: 'Intelligence', value: this.stats.intelligence, icon: '🧠' }
            ],
            energy: {
                current: Math.round(this.stats.energy),
                max: 100
            },
            experience: {
                current: this.stats.experience,
                max: 100 * this.stats.level,
                level: this.stats.level
            },
            achievements: this.achievements,
            history: this.history
        };
    }
}

class CharacterSimulation {
    constructor(characters, updateInterval = 5000) {
        this.characters = characters;
        this.updateInterval = updateInterval;
        this.simulationRunning = false;
        this.selectedCharacter = null;
    }

    init() {
        this.setupDOM();
        this.renderLocations();
        this.renderCharacters();
        this.createCharacterListPanel();
        this.startSimulation();
    }

    setupDOM() {
        document.body.style.cssText = `
            font-family: 'Arial', sans-serif;
            background-color: #1a1a2e;
            color: #ffffff;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        `;

        const container = document.createElement('div');
        container.id = 'simulation-container';
        container.style.cssText = `
            display: grid;
            grid-template-columns: 4fr 1fr;
            gap: 20px;
            padding: 20px;
            height: 100vh;
        `;
        document.body.appendChild(container);

        const locationsGrid = document.createElement('div');
        locationsGrid.id = 'locations-grid';
        locationsGrid.style.cssText = `
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
        `;

        const characterListPanel = document.createElement('div');
        characterListPanel.id = 'character-list-panel';
        characterListPanel.style.cssText = `
            background-color: #16213e;
            border-radius: 10px;
            padding: 15px;
            overflow-y: auto;
        `;

        container.appendChild(locationsGrid);
        container.appendChild(characterListPanel);

        this.createControlPanel();
    }

    createControlPanel() {
        const controlPanel = document.createElement('div');
        controlPanel.id = 'simulation-controls';
        controlPanel.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 15px;
            background-color: rgba(22, 33, 62, 0.8);
            padding: 10px;
            border-radius: 20px;
        `;

        const buttons = [
            { text: 'Pause/Resume', action: () => this.toggleSimulation() },
            { text: 'Speed Up', action: () => this.adjustSimulationSpeed(1000) },
            { text: 'Slow Down', action: () => this.adjustSimulationSpeed(10000) }
        ];

        buttons.forEach(btn => {
            const button = document.createElement('button');
            button.textContent = btn.text;
            button.style.cssText = `
                background-color: #0f3460;
                color: white;
                border: none;
                padding: 10px 15px;
                border-radius: 20px;
                cursor: pointer;
                transition: background-color 0.3s;
            `;
            button.onclick = btn.action;
            button.onmouseover = (e) => e.target.style.backgroundColor = '#e94560';
            button.onmouseout = (e) => e.target.style.backgroundColor = '#0f3460';
            controlPanel.appendChild(button);
        });

        document.body.appendChild(controlPanel);
    }

    createCharacterListPanel() {
        const panel = document.getElementById('character-list-panel');
        panel.innerHTML = '<h2>Character List</h2>';

        this.characters.forEach(character => {
            const characterCard = document.createElement('div');
            characterCard.style.cssText = `
                background-color: ${character.color}33;
                border-radius: 10px;
                padding: 10px;
                margin-bottom: 10px;
                cursor: pointer;
                transition: transform 0.3s;
            `;
            characterCard.innerHTML = `
                <h3>${character.name} (${character.personality})</h3>
                <p>Level ${character.stats.level} | ${character.location}</p>
            `;
            characterCard.onclick = () => this.showCharacterDetails(character);
            characterCard.onmouseover = (e) => e.target.style.transform = 'scale(1.05)';
            characterCard.onmouseout = (e) => e.target.style.transform = 'scale(1)';
            panel.appendChild(characterCard);
        });
    }

    renderLocations() {
        const locationsGrid = document.getElementById('locations-grid');
        locations.forEach(location => {
            const locationElement = document.createElement('div');
            locationElement.id = location.name;
            locationElement.style.cssText = `
                background: ${location.background};
                border-radius: 15px;
                padding: 15px;
                position: relative;
                display: flex;
                flex-direction: column;
                min-height: 200px;
                box-shadow: 0 10px 20px rgba(0,0,0,0.2);
                transition: transform 0.3s;
            `;

            locationElement.innerHTML = `
                <h2 style="color: white; text-align: center; margin-bottom: 10px;">
                    ${location.icon} ${location.name}
                </h2>
                <div id="${location.name}-characters" style="display: flex; flex-wrap: wrap;"></div>
            `;

            locationElement.onmouseover = (e) => {
                e.target.style.transform = 'scale(1.05)';
            };
            locationElement.onmouseout = (e) => {
                e.target.style.transform = 'scale(1)';
            };

            locationsGrid.appendChild(locationElement);
        });
    }

    renderCharacters() {
        this.characters.forEach(character => {
            const locationElement = document.getElementById(`${character.location}-characters`);
            const characterElement = document.createElement('div');
            characterElement.style.cssText = `
                background-color: ${character.color};
                border-radius: 50%;
                width: 50px;
                height: 50px;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 5px;
                cursor: pointer;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                transition: transform 0.3s;
            `;
            characterElement.textContent = character.name[0];
            characterElement.title = character.getStatusReport().basic;
            
            characterElement.onclick = () => this.showCharacterDetails(character);
            characterElement.onmouseover = (e) => {
                e.target.style.transform = 'scale(1.1)';
            };
            characterElement.onmouseout = (e) => {
                e.target.style.transform = 'scale(1)';
            };
            
            locationElement.appendChild(characterElement);
        });
    }

    showCharacterDetails(character) {
        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        `;

        const detailsContainer = document.createElement('div');
        detailsContainer.style.cssText = `
            background: #16213e;
            padding: 30px;
            border-radius: 20px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
            color: white;
        `;

        const statusReport = character.getStatusReport();
        detailsContainer.innerHTML = `
            <h2 style="color: ${character.color}; text-align: center;">${statusReport.basic}</h2>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <h3>Stats</h3>
                    ${statusReport.stats.map(stat => `
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span>${stat.icon} ${stat.name}</span>
                            <span>${stat.value}</span>
                        </div>
                    `).join('')}
                </div>
                <div>
                    <h3>Location</h3>
                    <p><strong>${statusReport.location.name}</strong> - ${statusReport.location.mood}</p>
                    <p>${statusReport.location.description}</p>
                    
                    <h3>Energy</h3>
                    <div style="background-color: #0f3460; border-radius: 10px; overflow: hidden;">
                        <div style="width: ${statusReport.energy.current}%; background-color: #e94560; height: 20px;"></div>
                    </div>
                    <p>${statusReport.energy.current}/${statusReport.energy.max}</p>
                </div>
            </div>

            <div>
                <h3>Experience</h3>
                <div style="background-color: #0f3460; border-radius: 10px; overflow: hidden;">
                    <div style="width: ${(statusReport.experience.current / statusReport.experience.max) * 100}%; background-color: #e94560; height: 20px;"></div>
                </div>
                <p>Level ${statusReport.experience.level} (${statusReport.experience.current}/${statusReport.experience.max})</p>
            </div>

            <div>
                <h3>Achievements</h3>
                ${statusReport.achievements.length > 0 
                    ? statusReport.achievements.map(a => `<p>🏆 ${a}</p>`).join('') 
                    : '<p>No achievements yet</p>'}
            </div>
        `;

        const closeButton = document.createElement('button');
        closeButton.textContent = 'Close';
        closeButton.style.cssText = `
            background-color: #e94560;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 20px;
            margin-top: 20px;
            width: 100%;
            cursor: pointer;
        `;
        closeButton.onclick = () => document.body.removeChild(overlay);

        detailsContainer.appendChild(closeButton);
        overlay.appendChild(detailsContainer);
        document.body.appendChild(overlay);
    }

    updateSimulation() {
        // Ensure locations exist before clearing
        locations.forEach(location => {
            const locationCharactersElement = document.getElementById(`${location.name}-characters`);
            if (locationCharactersElement) {
                locationCharactersElement.innerHTML = '';
            }
        });

        // Defensive check to ensure characters exist
        if (!this.characters || this.characters.length === 0) {
            console.warn('No characters in simulation');
            return;
        }

        // Update each character and re-render
        this.characters.forEach(character => {
            try {
                // Ensure character has an update method
                if (typeof character.update === 'function') {
                    character.update();
                }

                const locationCharactersElement = document.getElementById(`${character.location}-characters`);
                
                // Additional check to prevent null reference
                if (!locationCharactersElement) {
                    console.warn(`Location element not found for ${character.location}`);
                    return;
                }

                const characterElement = document.createElement('div');
                characterElement.style.cssText = `
                    background-color: ${character.color};
                    border-radius: 50%;
                    width: 50px;
                    height: 50px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin: 5px;
                    cursor: pointer;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    transition: transform 0.3s;
                `;
                characterElement.textContent = character.name[0];
                characterElement.title = character.getStatusReport().basic;
                
                characterElement.onclick = () => this.showCharacterDetails(character);
                characterElement.onmouseover = (e) => {
                    e.target.style.transform = 'scale(1.1)';
                };
                characterElement.onmouseout = (e) => {
                    e.target.style.transform = 'scale(1)';
                };
                
                locationCharactersElement.appendChild(characterElement);
            } catch (error) {
                console.error(`Error updating character ${character.name}:`, error);
            }
        });

        // Update character list panel
        this.createCharacterListPanel();
    }

    startSimulation() {
        this.simulationRunning = true;
        this.simulationTimer = setInterval(() => {
            if (this.simulationRunning) {
                this.updateSimulation();
            }
        }, this.updateInterval);
    }

    toggleSimulation() {
        this.simulationRunning = !this.simulationRunning;
        const controlPanel = document.getElementById('simulation-controls');
        const pauseButton = controlPanel.querySelector('button');
        pauseButton.textContent = this.simulationRunning ? 'Pause' : 'Resume';
    }

    adjustSimulationSpeed(newInterval) {
        clearInterval(this.simulationTimer);
        this.updateInterval = newInterval;
        this.startSimulation();
    }
}

// Initialize Characters
const characters = [
    new CharacterModel('Alice', '#FF6B6B'),
    new CharacterModel('Bob', '#4ECDC4'),
    new CharacterModel('Charlie', '#45B7D1'),
    new CharacterModel('Daisy', '#FDCB6E'),
    new CharacterModel('Ethan', '#6C5CE7')
];

// Start Simulation
document.addEventListener('DOMContentLoaded', () => {
    const simulation = new CharacterSimulation(characters);
    simulation.init();
});