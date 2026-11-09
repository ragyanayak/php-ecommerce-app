pipeline {
    agent any

    options {
        timestamps()
        disableConcurrentBuilds()
    }

    environment {
        IMAGE_NAME = 'ragyanayak/php-ecommerce'
        IMAGE_TAG = "${BUILD_NUMBER}"

        GITOPS_REPO = 'git@github.com:ragyanayak/php-ecommerce-gitops.git'
        GITOPS_BRANCH = 'main'
    }

    stages {

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('PHP Syntax Validation') {
            steps {
                sh '''
                    set -e

                    echo "Validating PHP files..."

                    docker run --rm \
                        -v "$WORKSPACE:/app" \
                        -w /app \
                        php:8.3-cli \
                        sh -c 'find . -name "*.php" -print0 | xargs -0 -r -n1 php -l'

                    echo "PHP syntax validation successful."
                '''
            }
        }

        stage('Docker Build') {
            steps {
                sh '''
                    set -e

                    echo "Building Docker image..."

                    docker build \
                        -t "$IMAGE_NAME:$IMAGE_TAG" .

                    echo "Docker build successful."
                '''
            }
        }

        stage('Docker Hub Push') {
            steps {
                withCredentials([
                    usernamePassword(
                        credentialsId: 'dockerhub-credentials',
                        usernameVariable: 'DOCKER_USER',
                        passwordVariable: 'DOCKER_TOKEN'
                    )
                ]) {
                    sh '''
                        set -e

                        echo "Logging in to Docker Hub..."

                        echo "$DOCKER_TOKEN" | docker login \
                            -u "$DOCKER_USER" \
                            --password-stdin

                        echo "Pushing Docker image..."

                        docker push "$IMAGE_NAME:$IMAGE_TAG"

                        docker logout

                        echo "Docker image pushed successfully."
                    '''
                }
            }
        }

        stage('Update GitOps Repository') {
            steps {
                sshagent(credentials: ['gitops-ssh-key']) {
                    sh '''
                        set -e

                        echo "Preparing SSH..."

                        mkdir -p ~/.ssh
                        chmod 700 ~/.ssh

                        ssh-keyscan -H github.com >> ~/.ssh/known_hosts
                        chmod 600 ~/.ssh/known_hosts

                        echo "Cloning GitOps repository..."

                        rm -rf gitops-work

                        git clone \
                            --branch "$GITOPS_BRANCH" \
                            "$GITOPS_REPO" \
                            gitops-work

                        cd gitops-work

                        echo "Updating Kubernetes image..."

                        if [ ! -f dev/deployment.yaml ]; then
                            echo "ERROR: dev/deployment.yaml not found!"
                            exit 1
                        fi

                        sed -i \
                            "s|image:.*|image: $IMAGE_NAME:$IMAGE_TAG|" \
                            dev/deployment.yaml

                        echo "Updated deployment manifest:"
                        grep 'image:' dev/deployment.yaml

                        git config user.name "Jenkins"
                        git config user.email "jenkins@localhost"

                        git add dev/deployment.yaml

                        if git diff --cached --quiet; then
                            echo "No GitOps changes to commit."
                        else
                            git commit \
                                -m "Update image to $IMAGE_TAG"

                            echo "Pushing changes to GitHub..."

                            git push origin "$GITOPS_BRANCH"
                        fi

                        echo "GitOps repository updated successfully."
                    '''
                }
            }
        }
    }

    post {
        success {
            echo "SUCCESS: Application image built, pushed, and GitOps repository updated."
        }

        failure {
            echo "FAILURE: Pipeline failed. Check the stage logs above."
        }

        always {
            echo "Pipeline execution completed."
        }
    }
}
