pipeline {

    agent any

    options {
        timestamps()
        disableConcurrentBuilds()
    }

    environment {

        IMAGE_NAME = 'ragyanayak/php-ecommerce'

        GITOPS_REPO =
            'git@github.com:ragyanayak/php-ecommerce-gitops.git'

        GITOPS_BRANCH = 'main'

        IMAGE_TAG = "${BUILD_NUMBER}"

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
                    docker run --rm \
                    -v "$WORKSPACE:/app" \
                    -w /app \
                    php:8.3-cli \
                    sh -c 'find . -name "*.php" -print0 | xargs -0 -n1 php -l'
                '''

            }

        }

        stage('Docker Build') {

            steps {

                sh '''
                    docker build \
                    -t $IMAGE_NAME:$IMAGE_TAG .
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
                        echo "$DOCKER_TOKEN" | \
                        docker login \
                        -u "$DOCKER_USER" \
                        --password-stdin

                        docker push $IMAGE_NAME:$IMAGE_TAG

                        docker logout
                    '''

                }

            }

        }

        stage('Update GitOps Repository') {

            steps {

                sshagent(credentials: ['gitops-ssh-key']) {

                    sh '''

                        mkdir -p ~/.ssh

                        ssh-keyscan -H github.com >> ~/.ssh/known_hosts

                        rm -rf gitops-work

                        git clone \
                        --branch $GITOPS_BRANCH \
                        $GITOPS_REPO \
                        gitops-work

                        cd gitops-work

                        sed -i \
                        "s|image:.*|image: $IMAGE_NAME:$IMAGE_TAG|" \
                        dev/deployment.yaml

                        git config user.name "ragyanayak"

                        git config user.email "ragyanayak143@gmail.com"

                        git add dev/deployment.yaml

                        git commit \
                        -m "Update image to $IMAGE_TAG"

                        git push origin $GITOPS_BRANCH

                    '''

                }

            }

        }

    }

    post {

        success {

            echo "Application build and GitOps update successful!"

        }

        failure {

            echo "Pipeline failed. Check Jenkins console logs."

        }

    }

}
